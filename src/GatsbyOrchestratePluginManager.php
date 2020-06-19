<?php

namespace Drupal\gatsby_orchestrator;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * GatsbyOrchestrate plugin manager.
 */
class GatsbyOrchestratePluginManager extends DefaultPluginManager {

  /**
   * Constructs GatsbyOrchestratePluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/GatsbyOrchestrate',
      $namespaces,
      $module_handler,
      'Drupal\gatsby_orchestrator\GatsbyOrchestrateInterface',
      'Drupal\gatsby_orchestrator\Annotation\GatsbyOrchestrate'
    );
    $this->alterInfo('gatsby_orchestrate_info');
    $this->setCacheBackend($cache_backend, 'gatsby_orchestrate_plugins');
  }

}
