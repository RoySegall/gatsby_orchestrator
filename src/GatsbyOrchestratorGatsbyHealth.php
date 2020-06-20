<?php

namespace Drupal\gatsby_orchestrator;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Messenger\MessengerInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Response;

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
   * @var string
   */
  protected $address;

  /**
   * @param string $address
   *
   * @retun $this
   */
  public function setAddress(string $address) {
    $this->address = $address;

    return $this;
  }

  /**
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $gatsbySettings;

  /**
   * @var MessengerInterface
   */
  protected $messenger;

  /**
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * GatsbyRevisionGatsbyHealth constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   * @param \GuzzleHttp\ClientInterface $http_client
   */
  public function __construct(\Drupal\Core\Config\ConfigFactoryInterface $config_factory, MessengerInterface $messenger, \GuzzleHttp\ClientInterface $http_client) {
    $this->gatsbySettings = $config_factory->get('gatsby.settings');
    $this->messenger = $messenger;
    $this->httpClient = $http_client;
  }

  /**
   * @return \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  public function getGatsbySettings() {
    return $this->gatsbySettings;
  }

  /**
   * @param \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig $gatsbySettings
   */
  public function setGatsbySettings($gatsbySettings) {
    $this->gatsbySettings = $gatsbySettings;

    return $this;
  }

  /**
   * @return MessengerInterface
   */
  public function getMessenger(): MessengerInterface {
    return $this->messenger;
  }

  /**
   * @param MessengerInterface $messenger
   */
  public function setMessenger(MessengerInterface $messenger) {
    $this->messenger = $messenger;

    return $this;
  }

  /**
   * @return \GuzzleHttp\ClientInterface
   */
  public function getHttpClient(): \GuzzleHttp\ClientInterface {
    return $this->httpClient;
  }

  /**
   * @param \GuzzleHttp\ClientInterface $httpClient
   */
  public function setHttpClient(\GuzzleHttp\ClientInterface $httpClient) {
    $this->httpClient = $httpClient;

    return $this;
  }

  /**
   * @return array|mixed|string|null
   */
  public function getAddress() {

    if (!$this->address) {
      $this->address = $this->gatsbySettings->get('server_url');
      return $this->address;
    }

    return $this->address;
  }

  /**
   * Get the status of the gatsby development service.
   *
   * @return bool
   *  True in case the server is up false if not.
   */
  public function checkGatsbyHealth() {
    $address = $this->getAddress();

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
