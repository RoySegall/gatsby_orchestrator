<?php

namespace Drupal\gatsby_deploy\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Gatsby Deploy routes.
 */
class GatsbyDeployEventListener extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  protected $request;

  /**
   * The controller constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Request $request) {
    $this->entityTypeManager = $entity_type_manager;
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('request_stack')->getCurrentRequest(),
    );
  }

  protected function getFrontendEnvironmentFromSecretKey() {
    $payload = json_decode($this->request->getContent());

    if (empty($payload->secret_key)) {
      return NULL;
    }

    $results = $this->entityTypeManager()
      ->getStorage('frontend_environment')
      ->getQuery()
      ->condition('settings.id', 'gatsby-plugin-trigger-deploy')
      ->condition('settings.secret_key', $payload->secret_key)
      ->execute();

    if ($results) {
      return reset($results);
    }

    return NULL;
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
    return AccessResult::allowedIf($this->getFrontendEnvironmentFromSecretKey() != NULL);
  }

  /**
   * Builds the response.
   */
  public function build() {
    $environment_id = $this->getFrontendEnvironmentFromSecretKey();
  }

}
