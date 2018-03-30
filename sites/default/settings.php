<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all envrionments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to insure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}
$settings['install_profile'] = 'minimal';

// activate environment specific configuration splits
// https://www.liip.ch/en/blog/advanced-drupal-8-cmi-workflows
if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {
  $config["config_split.config_split.{$_ENV['PANTHEON_ENVIRONMENT']}"]['status'] = TRUE;
} else {
  $config['config_split.config_split.dev']['status'] = TRUE;
}

// conditionally reroute email
if (isset($_ENV['PANTHEON_ENVIRONMENT']) && $_ENV['PANTHEON_ENVIRONMENT'] == 'live') {
  $config['reroute_email.settings']['enable'] = FALSE;
  $config['reroute_email.settings']['address'] = ''; // just log
} else {
  $config['reroute_email.settings']['enable'] = TRUE;
}

// alleviate trusted host setting warnings
// Pantheon isn't susceptible to them, but it still reduces noise
// https://pantheon.io/docs/settings-php/
if (defined('PANTHEON_ENVIRONMENT')) {
  if (in_array($_ENV['PANTHEON_ENVIRONMENT'], array('dev', 'test', 'live'))) {
    $settings['trusted_host_patterns'][] = "{$_ENV['PANTHEON_ENVIRONMENT']}-{$_ENV['PANTHEON_SITE_NAME']}.getpantheon.io";
    $settings['trusted_host_patterns'][] = "{$_ENV['PANTHEON_ENVIRONMENT']}-{$_ENV['PANTHEON_SITE_NAME']}.pantheon.io";
    $settings['trusted_host_patterns'][] = "{$_ENV['PANTHEON_ENVIRONMENT']}-{$_ENV['PANTHEON_SITE_NAME']}.pantheonsite.io";
    $settings['trusted_host_patterns'][] = "{$_ENV['PANTHEON_ENVIRONMENT']}-{$_ENV['PANTHEON_SITE_NAME']}.panth.io";

    # Replace value with custom domain(s) added in the site Dashboard
    $settings['trusted_host_patterns'][] = '^sfpbd.sfgov.org$';
  }
}
