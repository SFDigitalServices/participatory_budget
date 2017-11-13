<?php

namespace Drupal\administerusersbyrole\Plugin\administerusersbyrole\AccessManager;

use Drupal\administerusersbyrole\Plugin\administerusersbyrole\AccessManager\AccessManagerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\Config;
use Drupal\Component\Utility\Html;

/**
 * Simple access manager based on a configured set of safe roles.
 *
 * @AccessManager(
 *   id = "simple",
 *   label = @Translation("Simple"),
 *   description = @Translation("Simple configuration based on a set of safe roles defined on this page."),
 *   help = @Translation("Configure the set of safe roles below.  Access for an operation is granted only if the sub-admin has the base permission for the operation and if the target user's roles are all in the set of safe roles."),
 * )
 */
class AccessManagerSimple extends AccessManagerBase {

  /**
   * {@inheritdoc}
   */
  public function access(array $roles, $operation, AccountInterface $account) {
    if (!$this->preAccess($operation, $account)) {
      return AccessResult::neutral();
    }

    $allowed = $this->listRoles($operation, $account);
    $errors = array_diff($roles, $allowed);
    return AccessResult::allowedIf(!$errors);
  }

  /**
   * {@inheritdoc}
   */
  public function listRoles($operation, AccountInterface $account) {
    if (!$this->preAccess($operation, $account)) {
      return [];
    }

    $result = $this->config['roles'];
    if ($this->config['include_exclude'] == 'exclude') {
      $result = array_diff($this->allRoles(), $result);
    }
    else {
      $result = array_intersect($this->allRoles(), $result);
    }

    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function form() {
    $form['include_exclude'] = [
      '#type' => 'radios',
      '#title' => $this->t('Available roles'),
      '#options' => [
        'include' => $this->t('Selected roles'),
        'exclude' => $this->t('All roles except selected'),
      ],
      '#default_value' => $this->config['include_exclude'],
    ];

    $options = array_map(function ($item) { return Html::escape($item->label()); }, $this->allRoles(TRUE));
    $form['roles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Roles'),
      '#default_value' => $this->config['roles'] ?: [],
      '#options' => $options,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function formSave(Config $config, array $values) {
    $config->set('simple.include_exclude', $values['include_exclude']);
    $config->set('simple.roles', array_filter($values['roles']));
  }

}
