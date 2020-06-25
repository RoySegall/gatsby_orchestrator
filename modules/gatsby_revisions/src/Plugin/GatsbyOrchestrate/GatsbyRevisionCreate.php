<?php

namespace Drupal\gatsby_revisions\Plugin\GatsbyOrchestrate;

use Drupal\gatsby_orchestrator\GatsbyOrchestratePluginBase;
use Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth;

/**
 * Plugin implementation of the gatsby_orchestrate.
 *
 * @GatsbyOrchestrate(
 *   id = "create_revision",
 *   label = @Translation("Create revision"),
 *   description = @Translation("Creating a revision in the server")
 * )
 */
class GatsbyRevisionCreate extends GatsbyOrchestratePluginBase {

  /**
   * {@inheritDoc}
   */
  public function orchestrate() {

    if ($this->gatsbyHealth->checkGatsbyHealth() == GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_DOWN) {
      return NULL;
    }

    if ($response = $this->sendRequest('post', 'revision')) {
      return $response->revisionId;
    }

    return NULL;
  }

}
