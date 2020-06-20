<?php

namespace Drupal\gatsby_revisions\Plugin\GatsbyOrchestrate;

use Drupal\gatsby_orchestrator\GatsbyOrchestratePluginBase;
use Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth;

/**
 * Plugin implementation of the gatsby_orchestrate.
 *
 * @GatsbyOrchestrate(
 *   id = "get_revisions",
 *   label = @Translation("Get revisions"),
 *   description = @Translation("Get revision from the develop server of gatsby")
 * )
 */
class GatsbyRevisionsQuery extends GatsbyOrchestratePluginBase {

  /**
   * {@inheritDoc}
   */
  public function orchestrate() {
    if ($this->gatsbyHealth->checkGatsbyHealth() == GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_DOWN) {
      return;
    }

    return $this->sendRequest('get', 'revisions');
  }

}
