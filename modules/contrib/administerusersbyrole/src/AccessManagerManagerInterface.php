<?php

namespace Drupal\administerusersbyrole;

use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Interface for Plugin manager class for AccessManagerInterface.
 */
interface AccessManagerManagerInterface extends PluginManagerInterface {
  public function get();
}
