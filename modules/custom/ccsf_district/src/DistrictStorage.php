<?php

namespace Drupal\ccsf_district;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\ccsf_district\Entity\DistrictInterface;

/**
 * Defines the storage handler class for District entities.
 *
 * This extends the base storage class, adding required special handling for
 * District entities.
 *
 * @ingroup ccsf_district
 */
class DistrictStorage extends SqlContentEntityStorage implements DistrictStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(DistrictInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {district_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {district_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(DistrictInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {district_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('district_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
