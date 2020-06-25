<?php

namespace Drupal\gatsby_revisions;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\gatsby_revisions\Entity\GatsbyRevision;

/**
 * Provides a list controller for the gatsby revision entity type.
 */
class GatsbyRevisionListBuilder extends EntityListBuilder {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The redirect destination service.
   *
   * @var \Drupal\Core\Routing\RedirectDestinationInterface
   */
  protected $redirectDestination;

  /**
   * The current account of the user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentAccount;

  /**
   * Constructs a new GatsbyRevisionListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Core\Routing\RedirectDestinationInterface $redirect_destination
   *   The redirect destination service.
   * @param \Drupal\Core\Session\AccountProxyInterface $currect_account
   *   The account service.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $date_formatter, RedirectDestinationInterface $redirect_destination, AccountProxyInterface $currect_account) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $date_formatter;
    $this->redirectDestination = $redirect_destination;
    $this->currentAccount = $currect_account;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter'),
      $container->get('redirect.destination'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['table'] = parent::render();

    $total = $this->getStorage()
      ->getQuery()
      ->count()
      ->execute();

    $build['summary']['#markup'] = $this->t('Total gatsby revisions: @total', ['@total' => $total]);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Title');
    $header['description'] = $this->t('Description');
    $header['revision_id'] = $this->t('Gatsby identifier');
    $header['status'] = $this->t('Status');
    $header['error'] = $this->t('error');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $statuses = GatsbyRevision::getStatuses();

    /* @var $entity \Drupal\gatsby_revisions\GatsbyRevisionInterface */
    $row['title'] = $entity->toLink();
    $row['description'] = $entity->get('description')->value;
    $row['revision_id'] = $entity->get('gatsby_revision_number')->value;
    $row['status'] = $statuses[$entity->get('status')->value];
    $row['error'] = $entity->get('error')->value;
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    if ($this->currentAccount->getAccount()->hasPermission('revert revisions')) {
      $operations['revert'] = [
        'title' => $this->t('Revert to this revision'),
        'weight' => 10,
        'url' => Url::fromRoute('gatsby_revisions.revert_to_revision', ['gatsby_revision' => $entity->id()]),
      ];
    }

    $destination = $this->redirectDestination->getAsArray();

    foreach ($operations as $key => $operation) {
      $operations[$key]['query'] = $destination;
    }

    return $operations;
  }

}
