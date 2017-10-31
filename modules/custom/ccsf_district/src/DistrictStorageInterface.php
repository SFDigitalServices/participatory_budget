<?php

namespace Drupal\ccsf_district;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface DistrictStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of District revision IDs for a specific District.
   *
   * @param \Drupal\ccsf_district\Entity\DistrictInterface $entity
   *   The District entity.
   *
   * @return int[]
   *   District revision IDs (in ascending order).
   */
  public function revisionIds(DistrictInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as District author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   District revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\ccsf_district\Entity\DistrictInterface $entity
   *   The District entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(DistrictInterface $entity);

  /**
   * Unsets the language for all District with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
