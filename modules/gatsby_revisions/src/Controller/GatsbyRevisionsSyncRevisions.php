<?php

namespace Drupal\gatsby_revisions\Controller;

use Drupal\Core\Url;
use Drupal\Core\Controller\ControllerBase;
use Drupal\gatsby_orchestrator\GatsbyOrchestratePluginManager;
use Drupal\gatsby_revisions\Entity\GatsbyRevision;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Gatsby Revisions routes.
 */
class GatsbyRevisionsSyncRevisions extends ControllerBase {

  /**
   * The gatsby revision query plugin object.
   *
   * @var \Drupal\gatsby_revisions\Plugin\GatsbyOrchestrate\GatsbyRevisionsQuery
   */
  protected $getRevisionsQuery;

  /**
   * GatsbyRevisionsSyncRevisions constructor.
   *
   * @param \Drupal\gatsby_orchestrator\GatsbyOrchestratePluginManager $gatsby_orchestrator_plugin_manager
   *   The gatsby orchestrator plugin service.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function __construct(GatsbyOrchestratePluginManager $gatsby_orchestrator_plugin_manager) {
    $this->getRevisionsQuery = $gatsby_orchestrator_plugin_manager->createInstance('get_revisions');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.gatsby_orchestrate')
    );
  }

  /**
   * Creating a revision.
   *
   * @param mixed $gatsby_revision
   *   The revision identifier from gatsby.
   */
  public function createRevision($gatsby_revision) {
    $values = [
      'title' => "{$gatsby_revision} synced revision from gatsby",
      'gatsby_revision_number' => $gatsby_revision,
      'status' => GatsbyRevision::STATUS_PASSED,
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

    $gatsby_revisions = $this->getRevisionsQuery->orchestrate();

    $info = [
      '@skipped' => 0,
      '@created' => 0,
    ];

    foreach ($gatsby_revisions as $gatsby_revision) {

      if (!in_array((string) $gatsby_revision, $existing_revisions)) {
        $info['@created']++;
        $this->createRevision($gatsby_revision);
      }
      else {
        $info['@skipped']++;
      }
    }

    $second_info['@href'] = Url::fromRoute('entity.gatsby_revision.collection')->setAbsolute()->toString();

    $markup = $this->t('Revision were synced. @created item(s) were created. @skipped item(s) already exists thus were skipped.', $info);
    $markup .= $this->t('Go back to the list of revision <a href="@href">revision pages</a>.', $second_info);

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $markup,
    ];

    return $build;
  }

}
