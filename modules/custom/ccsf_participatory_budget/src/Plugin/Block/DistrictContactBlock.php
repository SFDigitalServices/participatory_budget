<?php

namespace Drupal\ccsf_participatory_budget\Plugin\Block;

use Drupal\Core\Block\BlockBase;

use Drupal\ccsf_participatory_budget\Controller\DistrictController;

/**
 * Provides a 'DistrictContact' Block.
 *
 * @Block(
 *   id = "district_contact_block",
 *   admin_label = @Translation("District contact info block"),
 *   category = @Translation("District"),
 * )
 */
class DistrictContactBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // if the route included a district number, load the district to display
    // the contact info for
    $districtNumber = \Drupal::routeMatch()->getParameter('districtNumber');

    if (!$districtNumber) {
      return array();
    }

    $districtId = DistrictController::getDistrictIdForNumber($districtNumber);

    return array(
      '#theme' => 'ccsf_participatory_budget__district_contact',
      '#district' => \Drupal::entityManager()->getStorage('district')->load($districtId),
    );
  }
}
