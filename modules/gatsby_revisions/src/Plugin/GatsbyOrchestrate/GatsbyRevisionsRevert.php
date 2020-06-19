<?php

namespace Drupal\gatsby_revisions\Plugin\GatsbyOrchestrate;

use Drupal\gatsby_orchestrator\GatsbyOrchestrateInterface;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\gatsby_orchestrator\GatsbyOrchestratePluginBase;
use Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * @var int
   */
  protected $revisionNumber;

  /**
   * Setting the revision number.
   *
   * @param $revision_number
   *  The revision identifier form the gatsby server.
   *
   * @return $this
   *  The instance of the object for chaining methods.
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
