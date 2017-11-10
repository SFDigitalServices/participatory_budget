<?php

/**
 * @file
 * Contains \Drupal\ccsf_participatory_budget\Controller\DistrictController
 */
namespace Drupal\ccsf_participatory_budget\Controller;

use Drupal\Core\Controller\ControllerBase;

class DistrictController extends ControllerBase {

  public static function getDistrictIdForNumber($districtNumber) {
    $districtQuery = \Drupal::entityQuery('district');
    $districtQuery->condition('field_district_number', $districtNumber);
    $ids = array_values($districtQuery->execute());

    if (count($ids) == 0) {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    } else if (count($ids) > 1) {
      \Drupal::logger('ccsf_participatory_budget')->error('Mulitple districts with same number ' . $districtNumber);
      throw new \Symfony\Component\HttpKernel\Exception\HttpException(500);
    }

    return $ids[0];
  }

  public function districtLandingPage($districtNumber) {
    $districtId = self::getDistrictIdForNumber($districtNumber);

    $nodeQuery = \Drupal::entityQuery('node');
    $nodeQuery->condition('type', 'district_landing_page');
    $nodeQuery->condition('field_district.target_id', $districtId);
    $nodeQuery->condition('status', TRUE);
    $ids = array_values($nodeQuery->execute());

    if (count($ids) == 0) {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    } else if (count($ids) > 1) {
      \Drupal::logger('ccsf_participatory_budget')->error('Mulitple landing pages with the same district' . $districtNumber);
      throw new \Symfony\Component\HttpKernel\Exception\HttpException(500);
    }

    $node = $this->entityTypeManager()->getStorage('node')->load($ids[0]);

    return $this->entityTypeManager()->getViewBuilder('node')->view($node);
  }
}
