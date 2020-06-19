<?php

namespace Drupal\gatsby_revisions\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\gatsby_orchestrator\GatsbyEventListenerPluginManager;
use Drupal\gatsby_revisions\Entity\GatsbyRevision;
use Drupal\gatsby_revisions\Plugin\GatsbyEventListener\GatsbyRevisionCreation;
use Laminas\Diactoros\Response\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Returns responses for Gatsby Revisions routes.
 */
class GatsbyRevisionEventsListener extends ControllerBase {

  /**
   * @var GatsbyEventListenerPluginManager
   */
  protected $eventPluginManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.gatsby_event_listener'));
  }

  /**
   * GatsbyRevisionEventsListener constructor.
   *
   * @param GatsbyEventListenerPluginManager $event_plugin_manager
   */
  public function __construct(GatsbyEventListenerPluginManager $event_plugin_manager) {
    $this->eventPluginManager = $event_plugin_manager;
  }

  /**
   * Custom access callback.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account) {
    // For now, allow to all until there's a support with the token.
    return AccessResult::allowed();
  }

  /**
   * Builds the response.
   */
  public function build() {

    if (\Drupal::request()->getMethod() != Request::METHOD_POST) {
      throw new MethodNotAllowedHttpException([Request::METHOD_POST]);
    }

    $decoded_content = json_decode(\Drupal::request()->getContent());

    /** @var GatsbyRevisionCreation $plugin */
    $plugin = $this->eventPluginManager->createInstance($decoded_content->event);

    $plugin->handle($decoded_content);

    return new JsonResponse(['message' => 'made it!'], Response::HTTP_ACCEPTED);
  }

}
