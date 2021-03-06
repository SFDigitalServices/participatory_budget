<?php

namespace Drupal\ccsf_district;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of District entities.
 *
 * @ingroup ccsf_district
 */
class DistrictListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['number'] = $this->t('District Number');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\ccsf_district\Entity\District */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.district.edit_form',
      ['district' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
