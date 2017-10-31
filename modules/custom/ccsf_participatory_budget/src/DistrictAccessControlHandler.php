<?php

namespace Drupal\ccsf_participatory_budget;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the District entity.
 *
 * @see \Drupal\ccsf_participatory_budget\Entity\District.
 */
class DistrictAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\ccsf_participatory_budget\Entity\DistrictInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished district entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published district entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit district entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete district entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add district entities');
  }

}
