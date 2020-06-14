<?php

namespace Drupal\gatsby_revisions;

use Drupal\Core\Messenger\Messenger;
use Symfony\Component\HttpFoundation\Response;

class GatsbyRevisionOrchestrator {

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
   * @var GatsbyRevisionGatsbyHealth
   */
  protected $gatsbyHealth;

  /**
   * GatsbyRevisionGatsbyHealth constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   * @param \Drupal\Core\Messenger\Messenger $messenger
   * @param \GuzzleHttp\Client $http_client
   * @param GatsbyRevisionGatsbyHealth $gatsby_health
   */
  public function __construct(\Drupal\Core\Config\ConfigFactory $config_factory, Messenger $messenger, \GuzzleHttp\Client $http_client, GatsbyRevisionGatsbyHealth $gatsby_health) {
    $this->gatsbySettings = $config_factory->get('gatsby.settings');
    $this->messenger = $messenger;
    $this->httpClient = $http_client;
    $this->gatsbyHealth = $gatsby_health;
  }

  /**
   * The method create revision via the gatsby revisions plugin.
   *
   * @return
   *  The newly crete revision.
   */
  public function createRevision($callback = NULL) {
    $address = $this->gatsbySettings->get('server_url');

    if ($this->gatsbyHealth->checkGatsbyHealth() == GatsbyRevisionGatsbyHealth::GATSBY_SERVICE_DOWN) {
      return;
    }

    try {
      $response = $this->httpClient->post($address . 'revision');
      $decoded_response = json_decode($response->getBody()->getContents());

      return $decoded_response->revisionId;

    } catch (\Exception $exception) {
      $this->messenger->addError($exception->getMessage());
      return;
    }
  }

}
