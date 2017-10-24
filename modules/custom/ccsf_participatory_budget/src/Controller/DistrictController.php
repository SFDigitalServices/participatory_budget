<?php

/**
 * @file
 * Contains \Drupal\ccsf_participatory_budget\Controller\DistrictController
 */
namespace Drupal\ccsf_participatory_budget\Controller;

use Drupal\Core\Controller\ControllerBase;

class DistrictController extends ControllerBase {
  public function content($number) {
    // Get all nodes of type page.
    $nodeQuery = \Drupal::entityQuery('node');
    $nodeQuery->condition('type', 'district');
    $nodeQuery->condition('field_district_number', $number);
    $nodeQuery->condition('status', TRUE);
    $ids = array_values($nodeQuery->execute());

    if (count($ids) == 0) {
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    } else if (count($ids) > 1) {
      \Drupal::logger('ccsf_participatory_budget')->error('Mulitple districts with same number ' . $number);
      throw new \Symfony\Component\HttpKernel\Exception\HttpException(500);
    }

    $node = $this->entityTypeManager()->getStorage('node')->load($ids[0]);

    return $this->entityTypeManager()->getViewBuilder('node')->view($node);
  }
}
