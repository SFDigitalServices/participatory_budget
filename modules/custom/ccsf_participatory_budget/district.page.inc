<?php

/**
 * @file
 * Contains district.page.inc.
 *
 * Page callback for District entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for District templates.
 *
 * Default template: district.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_district(array &$variables) {
  // Fetch District Entity Object.
  $district = $variables['elements']['#district'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
