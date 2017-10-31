<?php

namespace Drupal\ccsf_participatory_budget\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for District entities.
 */
class DistrictViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
