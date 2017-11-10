<?php

namespace Drupal\ccsf_participatory_budget\Plugin\Block;

use Drupal\Core\Block\BlockBase;

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
    $node = \Drupal::routeMatch()->getParameter('node');

    if (!$node || !$node->hasField('field_district')) {
      return array();
    }

    return array(
      '#theme' => 'ccsf_participatory_budget__district_contact',
      '#district' => $node->get('field_district')->entity,
    );
  }
}
