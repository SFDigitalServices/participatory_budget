<?php

namespace Drupal\administerusersbyrole\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    // Provide additional access according to our permissions.

    if ($route = $collection->get('entity.user.collection')) {
      $perm = $route->getRequirement('_permission') . '+access users overview';
      $route->setRequirement('_permission', $perm);
    }

    if ($route = $collection->get('user.multiple_cancel_confirm')) {
      $perm = $route->getRequirement('_permission') . '+access users overview';
      $route->setRequirement('_permission', $perm);
    }

    if ($route = $collection->get('user.admin_create')) {
      $perm = $route->getRequirement('_permission') . '+create users';
      $route->setRequirement('_permission', $perm);
    }
  }
}
