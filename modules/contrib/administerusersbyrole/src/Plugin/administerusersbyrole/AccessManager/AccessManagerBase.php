<?php

namespace Drupal\administerusersbyrole\Plugin\administerusersbyrole\AccessManager;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Config\Config;
use Drupal\administerusersbyrole\AccessManagerInterface;

/**
 * Base class for Access Manager plug-ins.
 *
 * @ingroup entity_api
 */
abstract class AccessManagerBase implements AccessManagerInterface {

  use StringTranslationTrait;

  protected $config;
  protected $id;
  protected $definition;

  const CONVERT_OP = [
    'cancel' => 'cancel',
    'delete' => 'cancel',
    'edit' => 'edit',
    'update' => 'edit',
    'view' => 'view',
    'role-assign' => 'role-assign',
  ];

  function __construct($config, $id, $definition) {
    $this->config = $config;
    $this->id = $id;
    $this->definition = $definition;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->definition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->definition['description'];
  }

  /**
   * {@inheritdoc}
   */
  public function getHelp() {
    return $this->definition['help'];
  }

  /**
   * {@inheritdoc}
   */
  public function permissions() {
    $op_titles = [
      'edit' => $this->t('Edit users by role'),
      'cancel' => $this->t('Cancel users by role'),
      'view' => $this->t('View users by role'),
      'role-assign' => $this->t('Assign roles by role'),
    ];

    foreach ($op_titles as $op => $title) {
      $perm_string = $this->buildPermString($op);
      $perms[$perm_string] = ['title' => $title];
    }
    return $perms;
  }

  /**
   * {@inheritdoc}
   */
  public function form() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function formSave(Config $config, array $values) {
  }

  /**
   * Initial access check for an operation to test if access might be granted for some roles.
   *
   * @param string $operation: The operation that is to be performed on the user.
   *   Value is updated to match the canonical value used in this module.
   *
   * @param \Drupal\Core\Session\AccountInterface $account: The account trying to access the entity.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result. hook_entity_access() has detailed documentation.
   */
  protected function preAccess(&$operation, AccountInterface $account) {
    // Full admins already have permissions so we are wasting our time to continue.
    if ($account->hasPermission('administer users')) {
      return FALSE;
    }

    // Ignore unrecognised operation.
    if (!isset(self::CONVERT_OP[$operation])) {
      return FALSE;
    }

    $operation = self::CONVERT_OP[$operation];
    return $this->hasPerm($operation, $account);
  }

  /**
   * Return array of all roles that are manageable by this module.
   */
  protected function allRoles($asObjects = FALSE) {
    $roles = user_roles(TRUE);

    // Exclude the AUTHENTICATED_ROLE which is not a real role.
    unset($roles[AccountInterface::AUTHENTICATED_ROLE]);

    // Exclude admin roles.  Once you can edit an admin, you can set their password, log in and do anything,
    // which defeats the point of using this module.
    $roles = array_filter($roles, function($role) { return !$role->isAdmin(); });

    return $asObjects ? $roles : array_keys($roles);
  }

  /**
   * Checks access to a permission for a given role name.
   */
  protected function hasPerm($op, AccountInterface $account, $rid = NULL) {
    return $account->hasPermission($this->buildPermString($op, $rid));
  }

  /**
   * Generates a permission string for a given role name.
   */
  protected function buildPermString($op, $rid = NULL) {
    return $rid ? "$op users with role $rid" : "$op users by role";
  }

}
