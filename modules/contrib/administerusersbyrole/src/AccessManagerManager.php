<?php

namespace Drupal\administerusersbyrole;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\administerusersbyrole\AccessManagerManagerInterface;

/**
 * Plugin manager class for AccessManagerInterface.
 *
 * @ingroup entity_api
 */
class AccessManagerManager extends DefaultPluginManager implements AccessManagerManagerInterface {

  /* @var \Drupal\administerusersbyrole\Plugin\administerusersbyrole\AccessManagerInterface $manager */
  protected $manager;

  /* @var \Drupal\Core\Config\ImmutableConfig */
  protected $config;

  /**
   * Constructs an AccessManagerManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, ConfigFactoryInterface $config_factory) {
    parent::__construct(
      'Plugin/administerusersbyrole/AccessManager',
      $namespaces,
      $module_handler,
      'Drupal\administerusersbyrole\AccessManagerInterface',
      'Drupal\administerusersbyrole\Annotation\AccessManager'
    );
    $this->alterInfo('administerusersbyrole_access_manager');
    $this->setCacheBackend($cache_backend, 'administerusersbyrole_access_manager');
    $this->config = $config_factory->get('administerusersbyrole.settings');
  }

  public function get() {
    if (!isset($this->manager)) {
      $mode = $this->config->get('mode');
      $instance_config = $this->config->get($mode) ?: [];
      $this->manager = $this->createInstance($mode, $instance_config);
    }
    return $this->manager;
  }

  public function getAll() {
    $plugins = [];

    foreach ($this->getDefinitions() as $id => $plugin) {
      $instance_config = $this->config->get($id) ?: [];
      $plugins[$id] = $this->createInstance($id, $instance_config);
    }
    return $plugins;
  }

}
