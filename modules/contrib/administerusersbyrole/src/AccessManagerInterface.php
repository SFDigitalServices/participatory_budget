<?php

namespace Drupal\administerusersbyrole;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Config\Config;

/**
 * Defines a common interface for access managers.
 */
interface AccessManagerInterface {

  /**
   * Return the plug-in label.
   */
  public function getLabel();

  /**
   * Return the plug-in description.
   */
  public function getDescription();

  /**
   * Return the plug-in help text.
   */
  public function getHelp();

  /**
   * Check access for the specified roles.
   *
   * @param array $roles: Roles of the user object to check access for.
   *
   * @param string $operation: The operation that is to be performed on the user.
   *
   * @param \Drupal\Core\Session\AccountInterface $account: The account trying to access the entity.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result. hook_entity_access() has detailed documentation.
   */
  public function access(array $roles, $operation, AccountInterface $account);

  /**
   * List all accessible roles for the specified operation.
   *
   * @param string $operation: The operation that is to be performed.
   *
   * @param \Drupal\Core\Session\AccountInterface $account: The account trying to access the entity.
   *
   * @return array of role IDs.
   */
  public function listRoles($operation, AccountInterface $account);

  /**
   * Return permissions to add.
   *
   * @return array of permissions.
   */
  public function permissions();

  /**
   * Return configuration form entries to add.
   *
   * @return form array.
   */
  public function form();

  /**
   * Save form values to config.
   *
   * @param \Drupal\Core\Config\Config $config: Config object to save to
   *
   * @param array $value: Form values
   */
  public function formSave(Config $config, array $values);

}
