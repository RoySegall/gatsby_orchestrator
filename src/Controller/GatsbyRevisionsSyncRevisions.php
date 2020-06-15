<?php

namespace Drupal\gatsby_revisions\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\gatsby_revisions\GatsbyRevisionOrchestrator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Gatsby Revisions routes.
 */
class GatsbyRevisionsSyncRevisions extends ControllerBase {

  /**
   * @var GatsbyRevisionOrchestrator
   */
  protected $gatsbyRevisionOrchestrator;

  /**
   * GatsbyRevisionsSyncRevisions constructor.
   *
   * @param GatsbyRevisionOrchestrator $gatsby_revision_orchestrator
   */
  public function __construct(GatsbyRevisionOrchestrator $gatsby_revision_orchestrator) {
    $this->gatsbyRevisionOrchestrator = $gatsby_revision_orchestrator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('gatsby_revision.orchestrator')
    );
  }

  /**
   * Creating a revision.
   *
   * @param $gatsby_revision
   *  The revision identifier from gatsby.
   */
  public function createRevision($gatsby_revision) {
    $values = [
      'title' => "{$gatsby_revision} synced revision from gatsby",
      'gatsby_revision_number' => $gatsby_revision,
    ];
    $this
      ->entityTypeManager()
      ->getStorage('gatsby_revision')
      ->create($values)
      ->save();
  }

  /**
   * Builds the response.
   */
  public function build() {
    $revisions_from_db = $this
      ->entityTypeManager()
      ->getStorage('gatsby_revision')
      ->loadMultiple();

    $existing_revisions = [];

    foreach ($revisions_from_db as $revision_from_db) {
      if (!$revision = $revision_from_db->get('gatsby_revision_number')->value) {
        continue;
      }

      $existing_revisions[] = $revision;
    }

    $gatsby_revisions = $this->gatsbyRevisionOrchestrator->getRevisions();

    $info = [
      '@skipped' => 0,
      '@created' => 0
    ];

    foreach ($gatsby_revisions as $gatsby_revision) {

      if (!in_array((string)$gatsby_revision, $existing_revisions)) {
        $info['@created']++;
        $this->createRevision($gatsby_revision);
      }
      else {
        $info['@skipped']++;
      }
    }

    $second_info['@href'] = \Drupal\Core\Url::fromRoute('entity.gatsby_revision.collection')->setAbsolute()->toString();

    $markup = $this->t('Revision were synced. @created item(s) were created. @skipped item(s) already exists thus were skipped.', $info);
    $markup .= $this->t('Go back to the list of revision <a href="@href">revision pages</a>.', $second_info);

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $markup,
    ];

    return $build;
  }

}
