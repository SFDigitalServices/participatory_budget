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
