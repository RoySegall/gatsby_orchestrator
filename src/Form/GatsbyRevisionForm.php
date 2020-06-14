<?php

namespace Drupal\gatsby_revisions\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\gatsby_revisions\GatsbyRevisionGatsbyHealth;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the gatsby revision entity edit forms.
 */
class GatsbyRevisionForm extends ContentEntityForm {

  /**
   * @var GatsbyRevisionGatsbyHealth
   */
  protected $gatsbyHealth;

  /**
   * GatsbyRevisionForm constructor.
   *
   * @param EntityRepositoryInterface $entity_repository
   * @param GatsbyRevisionGatsbyHealth $gatsby_health
   * @param EntityTypeBundleInfoInterface|null $entity_type_bundle_info
   * @param TimeInterface|null $time
   */
  public function __construct(
    EntityRepositoryInterface $entity_repository,
    GatsbyRevisionGatsbyHealth $gatsby_health,
    EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL,
    TimeInterface $time = NULL
  ) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);

    $this->gatsbyHealth = $gatsby_health;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('gatsby_revisions.gatsby_health'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);

    # If the service is alive then
    $actions['submit']['#disabled'] = $this->gatsbyHealth->checkGatsbyHealth() == GatsbyRevisionGatsbyHealth::GATSBY_SERVICE_DOWN;

    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {

    $entity = $this->getEntity();

    if ($entity->isNew()) {
      // First, trigger the request to create an revision it the gatsby server.
      // Next, create the entity and add the revision id to the entity.
    }

    $result = $entity->save();

    $link = $entity->toLink($this->t('View'))->toRenderable();

    $message_arguments = ['%label' => $this->entity->label()];
    $logger_arguments = $message_arguments + ['link' => render($link)];

    if ($result == SAVED_NEW) {
      $this->messenger()->addStatus($this->t('New gatsby revision %label has been created.', $message_arguments));
      $this->logger('gatsby_revisions')->notice('Created new gatsby revision %label', $logger_arguments);
    }
    else {
      $this->messenger()->addStatus($this->t('The gatsby revision %label has been updated.', $message_arguments));
      $this->logger('gatsby_revisions')->notice('Updated new gatsby revision %label.', $logger_arguments);
    }

    $form_state->setRedirect('entity.gatsby_revision.canonical', ['gatsby_revision' => $entity->id()]);
  }

}
