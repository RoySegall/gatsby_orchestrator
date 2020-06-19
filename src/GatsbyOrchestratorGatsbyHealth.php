<?php

namespace Drupal\gatsby_orchestrator;

use Drupal\Core\Messenger\Messenger;
use Symfony\Component\HttpFoundation\Response;

class GatsbyOrchestratorGatsbyHealth {

  const GATSBY_SERVICE_DOWN = 0;
  const GATSBY_SERVICE_UP = 1;

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
   * GatsbyRevisionGatsbyHealth constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   * @param \Drupal\Core\Messenger\Messenger $messenger
   * @param \GuzzleHttp\Client $http_client
   */
  public function __construct(\Drupal\Core\Config\ConfigFactory $config_factory, Messenger $messenger, \GuzzleHttp\Client $http_client) {
    $this->gatsbySettings = $config_factory->get('gatsby.settings');
    $this->messenger = $messenger;
    $this->httpClient = $http_client;
  }

  /**
   * Get the status of the gatsby development service.
   *
   * @return bool
   *  True in case the server is up false if not.
   */
  public function checkGatsbyHealth() {
    $address = $this->gatsbySettings->get('server_url');

    if (!$address) {
      $this->messenger->addError(t('It seems that the address of the gatsby server has not been set.'));
      return FALSE;
    }

    try {
      $response = $this->httpClient->get($address);

      if ($response->getStatusCode() == Response::HTTP_OK) {
        return self::GATSBY_SERVICE_UP;
      }

      return self::GATSBY_SERVICE_DOWN;
    } catch (\Exception $e) {
      $this->messenger->addError($e->getMessage());
      return self::GATSBY_SERVICE_DOWN;
    }
  }

}
