<?php

namespace Drupal\gatsby_revisions;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining a gatsby revision entity type.
 */
interface GatsbyRevisionInterface extends ContentEntityInterface {

  /**
   * Gets the gatsby revision title.
   *
   * @return string
   *   Title of the gatsby revision.
   */
  public function getTitle();

  /**
   * Sets the gatsby revision title.
   *
   * @param string $title
   *   The gatsby revision title.
   *
   * @return \Drupal\gatsby_revisions\GatsbyRevisionInterface
   *   The called gatsby revision entity.
   */
  public function setTitle($title);

  /**
   * Gets the gatsby revision creation timestamp.
   *
   * @return int
   *   Creation timestamp of the gatsby revision.
   */
  public function getCreatedTime();

  /**
   * Sets the gatsby revision creation timestamp.
   *
   * @param int $timestamp
   *   The gatsby revision creation timestamp.
   *
   * @return \Drupal\gatsby_revisions\GatsbyRevisionInterface
   *   The called gatsby revision entity.
   */
  public function setCreatedTime($timestamp);

}
