<?php

namespace Drupal\administerusersbyrole;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\administerusersbyrole\AccessManagerManagerInterface;

/**
 * Provides dynamic permissions of the administerusersbyrole module.
 */
class AdministerusersbyrolePermissions implements ContainerInjectionInterface {

 /**
   * The plug-in manager.
   *
   * @var \Drupal\administerusersbyrole\AccessManagerManagerInterface
   */
  protected $pluginManager;

  /**
   * Constructs a new AdministerusersbyrolePermissions instance.
   *
   * @param \Drupal\administerusersbyrole\AccessManagerManagerInterface $plugin_manager
   *   The entity manager.
   */
  public function __construct(AccessManagerManagerInterface $plugin_manager) {
    $this->pluginManager = $plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.administerusersbyrole'));
  }

  /**
   * Returns an array of administerusersbyrole permissions.
   *
   * @return array
   */
  public function permissions() {
    return $this->pluginManager->get()->permissions();
  }

}
