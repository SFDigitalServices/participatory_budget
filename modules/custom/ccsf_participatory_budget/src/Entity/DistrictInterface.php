<?php

namespace Drupal\ccsf_participatory_budget\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining District entities.
 *
 * @ingroup ccsf_participatory_budget
 */
interface DistrictInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the District name.
   *
   * @return string
   *   Name of the District.
   */
  public function getName();

  /**
   * Sets the District name.
   *
   * @param string $name
   *   The District name.
   *
   * @return \Drupal\ccsf_participatory_budget\Entity\DistrictInterface
   *   The called District entity.
   */
  public function setName($name);

  /**
   * Gets the District creation timestamp.
   *
   * @return int
   *   Creation timestamp of the District.
   */
  public function getCreatedTime();

  /**
   * Sets the District creation timestamp.
   *
   * @param int $timestamp
   *   The District creation timestamp.
   *
   * @return \Drupal\ccsf_participatory_budget\Entity\DistrictInterface
   *   The called District entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the District published status indicator.
   *
   * Unpublished District are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the District is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a District.
   *
   * @param bool $published
   *   TRUE to set this District to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\ccsf_participatory_budget\Entity\DistrictInterface
   *   The called District entity.
   */
  public function setPublished($published);

  /**
   * Gets the District revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the District revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\ccsf_participatory_budget\Entity\DistrictInterface
   *   The called District entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the District revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the District revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\ccsf_participatory_budget\Entity\DistrictInterface
   *   The called District entity.
   */
  public function setRevisionUserId($uid);

}
