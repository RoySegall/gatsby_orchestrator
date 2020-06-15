<?php

namespace Drupal\gatsby_revisions\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\example\ExampleInterface;
use Drupal\gatsby_revisions\GatsbyRevisionOrchestrator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Gatsby Revisions routes.
 */
class GatsbyRevisionRevertToRevision extends ControllerBase {

  /**
   * @var GatsbyRevisionOrchestrator
   */
  protected $gatsbyRevisionOrchestrator;

  /**
   * GatsbyRevisionRevertToRevision constructor.
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
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
