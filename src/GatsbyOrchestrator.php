<?php

namespace Drupal\gatsby_orchestrator;

use Drupal\Core\Messenger\Messenger;
use Drupal\gatsby_orchestrator\GatsbyOrchestratorGatsbyHealth;

class GatsbyOrchestrator {

  /**
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $gatsbySettings;

  /**
   * @var Messenger
   */
  protected $messenger;

  /**
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * @var GatsbyOrchestratorGatsbyHealth
   */
  protected $gatsbyHealth;

  /**
   * GatsbyRevisionGatsbyHealth constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   * @param \Drupal\Core\Messenger\Messenger $messenger
   * @param \GuzzleHttp\Client $http_client
   * @param GatsbyOrchestratorGatsbyHealth $gatsby_health
   */
  public function __construct(\Drupal\Core\Config\ConfigFactory $config_factory, Messenger $messenger, \GuzzleHttp\Client $http_client, GatsbyOrchestratorGatsbyHealth $gatsby_health) {
    $this->gatsbySettings = $config_factory->get('gatsby.settings');
    $this->messenger = $messenger;
    $this->httpClient = $http_client;
    $this->gatsbyHealth = $gatsby_health;
  }

  /**
   * A helper function to send generic requests to gatsby dev server.
   *
   * @param $method
   *  The method of the request: get, post etc. etc.
   * @param $endpoint
   *  The endpoint.
   *
   * @return mixed|void
   */
  protected function sendRequest($method, $endpoint) {
    $address = $this->gatsbySettings->get('server_url');

    try {
      $response = $this->httpClient->{$method}($address . $endpoint);
      return json_decode($response->getBody()->getContents());
    } catch (\Exception $e) {
      $this->messenger->addError($e->getMessage());
      return;
    }
  }

  /**
   * The method create revision via the gatsby revisions plugin.
   *
   * @return
   *  The newly crete revision.
   */
  public function createRevision() {
    if ($this->gatsbyHealth->checkGatsbyHealth() == GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_DOWN) {
      return;
    }

    if ($response = $this->sendRequest('post', 'revision')) {
      return $response->revisionId;
    }

    return;
  }

  /**
   * Get all the revisions from gatsby and create a reference.
   */
  public function getRevisions() {
    if ($this->gatsbyHealth->checkGatsbyHealth() == GatsbyOrchestratorGatsbyHealth::GATSBY_SERVICE_DOWN) {
      return;
    }

    return $this->sendRequest('get', 'revisions');
  }

  /**
   * Reverting the gatsby build to a revision.
   *
   * @param $revision_number
   *  The revision number.
   *
   * @return
   *  The response of the action.
   */
  public function revert($revision_number) {
    return $this->sendRequest('post', 'revision-revert/' . $revision_number);
  }

}
