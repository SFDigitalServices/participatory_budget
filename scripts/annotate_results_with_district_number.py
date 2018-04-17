#!/usr/bin/env python2

import argparse
import csv
import json
import sys
import urllib
import urllib2

def parse_args():
    parser = argparse.ArgumentParser(
        description='Create CSV from ballot submission CSV with address information annotated',
        formatter_class=argparse.ArgumentDefaultsHelpFormatter,
    )

    parser.add_argument("-a", "--address-field", default='Address', help="Name of the address field in the submissions")
    parser.add_argument("-k", "--google-api-key", help="Google API key with access to the geocoder API")
    parser.add_argument("-g", "--google-geocoder-url", default='https://maps.googleapis.com/maps/api/geocode/json', help="URL to Google geocoder")
    parser.add_argument("-G", "--gis-geocoder-url", default='https://sfgis-portal.sfgov.org/svc/rest/services/loc/c83_eas_str_ctrl_composite/GeocodeServer/findAddressCandidates', help="URL to GIS geocoder")
    parser.add_argument("submission_csv", help="Ballot submission CSV")
    args = parser.parse_args()

    if not args.google_api_key:
        parser.error('Google API key required')

    return args

# use Google to do the initial address geocoding since it is more lenient than the GIS server and the addresses are
# freeform
# TODO consider running the address through our GIS _first_ and hit Google if it doesn't get any results
def google_geocode_address(url, api_key, address):
    params = {
        'address': address,
        'key': api_key,
        'bounds': '-122.3533,37.85479|-122.542190,37.673494', # rough bounds of San Francisco
        'components': 'administrative_area:CA',
    }

    contents = urllib2.urlopen(url + "?" + urllib.urlencode(params))
    results = json.load(contents)['results']

    result = next(iter(results or []), None)
    if not result:
        return None

    # Parse address component long names out
    for item in result['address_components']:
	for category in item['types']:
	    result.setdefault(category, {})
	    result[category] = item['long_name']

    return result

def geocode_address(google_url, google_api_key, service_url, address):
    google_address = google_geocode_address(google_url, google_api_key, address)

    if not google_address or 'street_number' not in google_address:
        return None

    params = {
        'Street': google_address['street_number'] + ' ' + google_address['route'],
        'ZIP': google_address['postal_code'],
        'outFields': 'User_fld', # custom attribute holding the supervisorial district
        'f': 'pjson',
    }
    contents = urllib2.urlopen(service_url + "?" + urllib.urlencode(params))

    results = json.load(contents)['candidates']
    results.sort(key=lambda x:x['score'], reverse=True)

    return next(iter(results or []), None)

def main():
    args = parse_args()

    with open(args.submission_csv, 'rb') as csvfile:
        reader = csv.reader(csvfile)
        headers = reader.next()

    annotated_submission_writer = csv.DictWriter(sys.stdout, headers + [
        'normalized_address',
        'normalized_address_score',
        'normalized_address_district',
    ])
    annotated_submission_writer.writeheader()

    with open(args.submission_csv, 'rb') as csvfile:
        submissions = csv.DictReader(csvfile)
        for submission in submissions:
            if 'normalized_address' in submission and submission['normalized_address']:
                annotated_submission_writer.writerow(submission)
                continue

            result = geocode_address(args.google_geocoder_url, args.google_api_key, args.gis_geocoder_url, submission[args.address_field])

            # try again, appending the city since many entries only contain a street address
            if not result:
                result = geocode_address(args.google_geocoder_url, args.google_api_key, args.gis_geocoder_url, submission[args.address_field] + ' San Francisco')

            if result:
                submission.update({
                    'normalized_address': result['address'],
                    'normalized_address_score': result['score'],
                    'normalized_address_district': result['attributes']['User_fld'],
                })

            annotated_submission_writer.writerow(submission)

if __name__ == "__main__":
    main()
