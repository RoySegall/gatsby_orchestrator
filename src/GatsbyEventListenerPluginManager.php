<?php

namespace Drupal\gatsby_orchestrator;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * GatsbyEventListener plugin manager.
 */
class GatsbyEventListenerPluginManager extends DefaultPluginManager {

  /**
   * Constructs GatsbyEventListenerPluginManager object.
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
      'Plugin/GatsbyEventListener',
      $namespaces,
      $module_handler,
      'Drupal\gatsby_orchestrator\GatsbyEventListenerInterface',
      'Drupal\gatsby_orchestrator\Annotation\GatsbyEventListener'
    );
    $this->alterInfo('gatsby_event_listener_info');
    $this->setCacheBackend($cache_backend, 'gatsby_event_listener_plugins');
  }

}
