<?php

namespace Drupal\gatsby_orchestrator;

use GuzzleHttp\ClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * The gatsby healths service.
 */
class GatsbyOrchestratorGatsbyHealth {

  /**
   * The status when the gatsby development server is not running.
   */
  const GATSBY_SERVICE_DOWN = 0;

  /**
   * The status when the gatsby development server is running.
   */
  const GATSBY_SERVICE_UP = 1;

  /**
   * The gatsby setting config factory.
   *
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $gatsbySettings;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The http client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * GatsbyRevisionGatsbyHealth constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger object.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The client object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, MessengerInterface $messenger, ClientInterface $http_client) {
    $this->gatsbySettings = $config_factory->get('gatsby.settings');
    $this->messenger = $messenger;
    $this->httpClient = $http_client;
  }

  /**
   * Set the http client.
   *
   * @param \GuzzleHttp\ClientInterface $httpClient
   *   The http client.
   */
  public function setHttpClient(ClientInterface $httpClient) {
    $this->httpClient = $httpClient;

    return $this;
  }

  /**
   * Setting the settings service.
   *
   * @param \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig $gatsbySettings
   *   The replacement.
   *
   * @retrn GatsbyOrchestratorGatsbyHealth
   *   The current object.
   */
  public function setGatsbySettings($gatsbySettings) {
    $this->gatsbySettings = $gatsbySettings;

    return $this;
  }

  /**
   * Setting the messenger service.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger object.
   *
   * @return GatsbyOrchestratorGatsbyHealth
   *   The current object.
   */
  public function setMessenger(MessengerInterface $messenger) {
    $this->messenger = $messenger;

    return $this;
  }

  /**
   * Get the status of the gatsby development service.
   *
   * @return int
   *   True in case the server is up false if not.
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
    }
    catch (\Exception $e) {
      $this->messenger->addError($e->getMessage());
      return self::GATSBY_SERVICE_DOWN;
    }
  }

}
