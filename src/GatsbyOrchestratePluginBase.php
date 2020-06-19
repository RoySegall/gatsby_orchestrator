<?php

namespace Drupal\gatsby_orchestrator;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Messenger\Messenger;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for gatsby_orchestrate plugins.
 */
abstract class GatsbyOrchestratePluginBase extends PluginBase implements GatsbyOrchestrateInterface {

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
   * GatsbyOrchestratePluginBase constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   * @param Messenger $messenger
   * @param \GuzzleHttp\Client $http_client
   * @param GatsbyOrchestratorGatsbyHealth $gatsby_health
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    \Drupal\Core\Config\ConfigFactory $config_factory,
    Messenger $messenger,
    \GuzzleHttp\Client $http_client,
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
   * Trigger the action we need to do.
   */
  abstract function orchestrate();
}
