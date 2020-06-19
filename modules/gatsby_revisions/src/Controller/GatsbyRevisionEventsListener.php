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
    $logger = $this->getLogger('gatsby_revision');
    $storage = $this->entityTypeManager()->getStorage('gatsby_revision');

    $gatsby_revision_ids = $storage->getQuery()->condition('gatsby_revision_number', $decoded_content->revisionId)->execute();

    if (!$gatsby_revision_ids) {
      $params = [
        '@id' => $decoded_content->revisionId,
      ];
      $logger->error(t('A notification for the gatsby revision with the ID @id was sent but there is no record in the DB for a revision like that', $params));
      return;
    }

    /** @var GatsbyRevision $gatsby_revision */
    $gatsby_revision = $storage->load(reset($gatsby_revision_ids));

    if ($decoded_content->status == 'succeeded') {
      $gatsby_revision->set('status', GatsbyRevision::STATUS_PASSED);
    } else {
      $gatsby_revision->set('status', GatsbyRevision::STATUS_FAILED);
      $gatsby_revision->set('error', $decoded_content->data);
    }

    $gatsby_revision->save();
  }

}
