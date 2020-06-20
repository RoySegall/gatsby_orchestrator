<?php

namespace Drupal\gatsby_revisions\Plugin\GatsbyEventListener;

use Drupal\gatsby_orchestrator\GatsbyEventListenerPluginBase;
use Drupal\gatsby_revisions\Entity\GatsbyRevision;

/**
 * Plugin implementation of the gatsby_event_listener.
 *
 * @GatsbyEventListener(
 *   id = "revision_creation",
 *   label = @Translation("Revision creation"),
 *   description = @Translation("Handelign event when the revision creation event has sent.")
 * )
 */
class GatsbyRevisionCreation extends GatsbyEventListenerPluginBase {

  /**
   * {@inheritDoc}
   */
  public function handle($payload) {
    $storage = $this->entityTypeManager->getStorage('gatsby_revision');

    $gatsby_revision_ids = $storage
      ->getQuery()
      ->condition('gatsby_revision_number', $payload->revisionId)
      ->execute();

    if (!$gatsby_revision_ids) {
      $params = [
        '@id' => $payload->revisionId,
      ];
      $this->logger->error(t('A notification for the gatsby revision with the ID @id was sent but there is no record in the DB for a revision like that', $params));
      return;
    }

    /** @var \Drupal\gatsby_revisions\Entity\GatsbyRevision $gatsby_revision */
    $gatsby_revision = $storage->load(reset($gatsby_revision_ids));

    if ($payload->status == 'succeeded') {
      $gatsby_revision->set('status', GatsbyRevision::STATUS_PASSED);
      $this->logger->info('The gatsby revision, @title, set with the status success.', ['@title' => $gatsby_revision->label()]);
    }
    else {
      $gatsby_revision->set('status', GatsbyRevision::STATUS_FAILED);
      $gatsby_revision->set('error', $payload->data);

      $params = [
        '@title' => $gatsby_revision->label(),
        '@error' => $payload->data,
      ];

      $this->logger->info('The gatsby revision, @title, set with the status failed: @error.', $params);
    }

    $gatsby_revision->save();
  }

}
