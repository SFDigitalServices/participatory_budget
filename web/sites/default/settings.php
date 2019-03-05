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
  *  Place the config directory outside of the Drupal root.
  **/
$config_directories = array(
  CONFIG_SYNC_DIRECTORY => dirname(DRUPAL_ROOT) . '/config',
);

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

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}
$settings['install_profile'] = 'minimal';


/**
*  Force HTTPS
**/
if (isset($_ENV['PANTHEON_ENVIRONMENT']) && php_sapi_name() != 'cli') {
  // Redirect to https://$primary_domain in the Live environment
  if ($_ENV['PANTHEON_ENVIRONMENT'] === 'live') {
    $primary_domain = 'sfpbd.sfgov.org';
  }
  else {
    // Redirect to HTTPS on every Pantheon environment.
    $primary_domain = $_SERVER['HTTP_HOST'];
  }

  if ($_SERVER['HTTP_HOST'] != $primary_domain
      || !isset($_SERVER['HTTP_USER_AGENT_HTTPS'])
      || $_SERVER['HTTP_USER_AGENT_HTTPS'] != 'ON' ) {

    # Name transaction "redirect" in New Relic for improved reporting (optional)
    if (extension_loaded('newrelic')) {
      newrelic_name_transaction("redirect");
    }

    header('HTTP/1.0 301 Moved Permanently');
    header('Location: https://'. $primary_domain . $_SERVER['REQUEST_URI']);
    exit();
  }
  // Drupal 8 Trusted Host Settings
  if (is_array($settings)) {
    $settings['trusted_host_patterns'] = array('^'. preg_quote($primary_domain) .'$');
  }
}

$settings['hash_salt'] = 'f9535a87672981e9f8782207a55eed7fb6a5aded';
