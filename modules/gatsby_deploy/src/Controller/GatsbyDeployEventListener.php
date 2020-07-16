<?php

namespace Drupal\gatsby_deploy\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

  /**
   * The request object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The controller constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
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

  /**
   * Get the frontend environment by the secret key.
   *
   * We need the ID so we can display the logs of deployment.
   *
   * @return mixed|null
   *   In case we found a matching frontend environment the ID of the frontend
   *   environment will be return.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
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
   * {@inheritDoc}
   */
  public function access(AccountInterface $account) {
    return AccessResult::allowedIf($this->getFrontendEnvironmentFromSecretKey() != NULL);
  }

  /**
   * Creating records.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function build() {
    $environment_id = $this->getFrontendEnvironmentFromSecretKey();
    $payload = json_decode($this->request->getContent());

    $entity = $this
      ->entityTypeManager
      ->getStorage('gatsby_deploy')
      ->create([
        'frontend_environment' => $environment_id,
        'status' => $payload->status == 'succeeded' ?
          \Drupal\gatsby_deploy\Entity\GatsbyDeploy::STATUS_PASSED :
          \Drupal\gatsby_deploy\Entity\GatsbyDeploy::STATUS_FAILED,
        'created_at' => time(),
      ]);

    $entity->save();

    return new JsonResponse(['message' => 'Created successfully'], Response::HTTP_CREATED);
  }

}
