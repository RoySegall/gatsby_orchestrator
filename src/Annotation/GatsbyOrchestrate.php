<?php

namespace Drupal\gatsby_orchestrator\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines gatsby_orchestrate annotation object.
 *
 * @Annotation
 */
class GatsbyOrchestrate extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;

  /**
   * The description of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

}
