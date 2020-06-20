<?php

namespace Drupal\gatsby_revisions\Plugin\GatsbyOrchestrate;

use Drupal\gatsby_orchestrator\GatsbyOrchestratePluginBase;

/**
 * Plugin implementation of the gatsby_orchestrate.
 *
 * @GatsbyOrchestrate(
 *   id = "revert_revision",
 *   label = @Translation("Revert a revision"),
 *   description = @Translation("Send a request to rvert to a specific revision")
 * )
 */
class GatsbyRevisionsRevert extends GatsbyOrchestratePluginBase {

  /**
   * The revision number.
   *
   * @var int
   */
  protected $revisionNumber;

  /**
   * Setting the revision number.
   *
   * @param mixed $revision_number
   *   The revision identifier form the gatsby server.
   *
   * @return $this
   *   The instance of the object for chaining methods.
   */
  public function setRevisionNumber($revision_number) {
    $this->revisionNumber = $revision_number;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function orchestrate() {
    return $this->sendRequest('post', 'revision-revert/' . $this->revisionNumber);
  }

}
