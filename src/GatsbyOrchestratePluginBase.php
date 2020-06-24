<?php

namespace Drupal\gatsby_orchestrator;

use Drupal\Core\Config\Config;
use Drupal\Core\Messenger\MessengerInterface;
use GuzzleHttp\Client;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Messenger\Messenger;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for gatsby_orchestrate plugins.
 */
abstract class GatsbyOrchestratePluginBase extends PluginBase implements GatsbyOrchestrateInterface {

  /**
   * The gatsby settings factory.
   *
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $gatsbySettings;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * The http client service.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The gatsby health service.
   *
   * @var GatsbyOrchestratorGatsbyHealth
   */
  protected $gatsbyHealth;

  /**
   * Setting the messenger service.
   *
   * @param MessengerInterface $messenger
   *   The messenger object.
   *
   * @return GatsbyOrchestratePluginBase
   *   The current object.
   */
  public function setMessenger(MessengerInterface $messenger) {
    $this->messenger = $messenger;

    return $this;
  }

  /**
   * Setting the client service.
   *
   * @param Client $httpClient
   *   The client object.
   *
   * @return GatsbyOrchestratePluginBase
   *   The current object.
   */
  public function setHttpClient(Client $httpClient) {
    $this->httpClient = $httpClient;

    return $this;
  }

  /**
   * Setting the gatsby health service.
   *
   * @param GatsbyOrchestratorGatsbyHealth $gatsbyHealth
   *   The gatsby health object.
   *
   * @return GatsbyOrchestratePluginBase
   *   The current object.
   */
  public function setGatsbyHealth(GatsbyOrchestratorGatsbyHealth $gatsbyHealth) {
    $this->gatsbyHealth = $gatsbyHealth;

    return $this;
  }

  /**
   * Setting the gatsby settings.
   *
   * @param \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig $gatsbySettings
   *   The gatsby settings object.
   *
   * @return GatsbyOrchestratePluginBase
   *   The current object.
   */
  public function setGatsbySettings(Config $gatsbySettings) {
    $this->gatsbySettings = $gatsbySettings;

    return $this;
  }

  /**
   * GatsbyOrchestratePluginBase constructor.
   *
   * @param array $configuration
   *   The array configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param array $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factor service.
   * @param \Drupal\Core\Messenger\Messenger $messenger
   *   The messenger service.
   * @param \GuzzleHttp\Client $http_client
   *   The http client service.
   * @param GatsbyOrchestratorGatsbyHealth $gatsby_health
   *   The gatsby health service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    array $plugin_definition,
    ConfigFactory $config_factory,
    Messenger $messenger,
    Client $http_client,
    GatsbyOrchestratorGatsbyHealth $gatsby_health
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->gatsbySettings = $config_factory->get('gatsby.settings');
    $this->messenger = $messenger;
    $this->httpClient = $http_client;
    $this->gatsbyHealth = $gatsby_health;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('messenger'),
      $container->get('http_client'),
      $container->get('gatsby_orchestrator.gatsby_health')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    // Cast the label to a string since it is a TranslatableMarkup object.
    return (string) $this->pluginDefinition['label'];
  }

  /**
   * A helper function to send generic requests to gatsby dev server.
   *
   * @param string $method
   *   The method of the request: get, post etc. etc.
   * @param string $endpoint
   *   The endpoint.
   *
   * @return mixed
   *   Decoded object of the response if not error has happens.
   */
  protected function sendRequest($method, $endpoint) {
    $address = $this->gatsbySettings->get('server_url');

    try {
      $response = $this->httpClient->{$method}($address . $endpoint);
      return json_decode($response->getBody()->getContents());
    }
    catch (\Exception $e) {
      $this->messenger->addError($e->getMessage());
      return;
    }
  }

  /**
   * Trigger the action we need to do.
   *
   * @return mixed
   *   Any data the plugin desire to return.
   */
  abstract public function orchestrate();

}
