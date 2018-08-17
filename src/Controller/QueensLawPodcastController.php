<?php

namespace Drupal\queenslaw_podcast\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller routines for Queen's University Faculty of Law Podcast routes.
 */
class QueensLawPodcastController extends ControllerBase {

  public function data() {
    $response = new Response();
    $response->headers->set('Content-Type', 'text/xml');
    if ($content = _queenslaw_podcast_content()) $response->setContent($content);
    return $response;
  }

}
