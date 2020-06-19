<?php

namespace Drupal\gatsby_revisions\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\gatsby_revisions\Entity\GatsbyRevision;
use Laminas\Diactoros\Response\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Returns responses for Gatsby Revisions routes.
 */
class GatsbyRevisionEventsListener extends ControllerBase {

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

    if ($decoded_content->event == 'revision_creation') {
      // todo: export to a plugin.
      $this->updateGatsbyRevisionEntity($decoded_content);
    }

    return new JsonResponse(['message' => 'made it!'], Response::HTTP_ACCEPTED);
  }

  /**
   * Handle the revision status change.
   * @param $decoded_content
   */
  public function updateGatsbyRevisionEntity($decoded_content) {
    $storage = $this->entityTypeManager()->getStorage('gatsby_revision');

    $results = $storage->getQuery()
      ->condition('gatsby_revision_number', $decoded_content->revisionId)
      ->execute();

    if (!$results) {
      // todo: log.
      return;
    }

    /** @var GatsbyRevision $entity */
    $entity = $storage->load(reset($results));

    // todo: check if it failed.
    $entity->set('status', GatsbyRevision::STATUS_PASSED);
//    $entity->set('error', '');

    $entity->save();
    // todo: log.
  }

}
