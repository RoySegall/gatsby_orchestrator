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
 *   id = "create_revision",
 *   label = @Translation("Create revision"),
 *   description = @Translation("Creating a revision in the server")
 * )
 */
class GatsbyRevisionCreate extends GatsbyOrchestratePluginBase {

  /**
   * {@inheritDoc}
   */
  public function trigger() {

    if ($this->gatsbyHealth->checkGatsbyHealth() == GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_DOWN) {
      return;
    }

    if ($response = $this->sendRequest('post', 'revision')) {
      return $response->revisionId;
    }

    return;
  }

}
