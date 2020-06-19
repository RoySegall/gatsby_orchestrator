<?php

namespace Drupal\gatsby_orchestrator;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Interface for gatsby_event_listener plugins.
 */
interface GatsbyEventListenerInterface extends ContainerFactoryPluginInterface {

  /**
   * Returns the translated plugin label.
   *
   * @return string
   *   The translated title.
   */
  public function label();

}
