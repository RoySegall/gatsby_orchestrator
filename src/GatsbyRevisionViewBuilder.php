<?php

namespace Drupal\gatsby_revisions;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Provides a view controller for a gatsby revision entity type.
 */
class GatsbyRevisionViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  protected function getBuildDefaults(EntityInterface $entity, $view_mode) {
    $build = parent::getBuildDefaults($entity, $view_mode);
    // The gatsby revision has no entity template itself.
    unset($build['#theme']);
    return $build;
  }

}
