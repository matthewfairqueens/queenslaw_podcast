<?php
/**
 * @file
 * Contains \Drupal\queenslaw_podcast\Routing\QueensLawPodcastRouting
 */

namespace Drupal\queenslaw_podcast\Routing;

use Symfony\Component\Routing\Route;

/**
 * Defines dynamic routes.
 */
class QueensLawPodcastRouting {

  /**
   * {@inheritdoc}
   */
  public function routing() {
    $path = _queenslaw_podcast_path();
    $routes = [
      'queenslaw_podcast' => new Route(
        $path,
        [
          '_controller' => '\Drupal\queenslaw_podcast\Controller\QueensLawPodcastController::data',
        ],
        [
          '_permission'  => 'access content',
        ]
      ),
    ];
    return $routes;
  }

}
