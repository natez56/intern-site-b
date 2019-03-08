<?php

namespace Drupal\internsd8\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller routines for block example routes.
 */
class Internsd8Controller extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   *   Return markup array.
   */
  public function content() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t("<h1>Hello, World!</h1>"),
    ];
  }

}