<?php

namespace Drupal\gatsby_revisions\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\gatsby_revisions\GatsbyRevisionInterface;

/**
 * Defines the gatsby revision entity class.
 *
 * @ContentEntityType(
 *   id = "gatsby_revision",
 *   label = @Translation("Gatsby Revision"),
 *   label_collection = @Translation("Gatsby Revisions"),
 *   handlers = {
 *     "view_builder" = "Drupal\gatsby_revisions\GatsbyRevisionViewBuilder",
 *     "list_builder" = "Drupal\gatsby_revisions\GatsbyRevisionListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\gatsby_revisions\GatsbyRevisionAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\gatsby_revisions\Form\GatsbyRevisionForm",
 *       "edit" = "Drupal\gatsby_revisions\Form\GatsbyRevisionForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "gatsby_revision",
 *   admin_permission = "administer gatsby revision",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/content/gatsby-revision/add",
 *     "canonical" = "/admin/content/gatsby-revision/{gatsby_revision}",
 *     "edit-form" = "/admin/content/gatsby-revision/{gatsby_revision}/edit",
 *     "delete-form" = "/admin/content/gatsby-revision/{gatsby_revision}/delete",
 *     "collection" = "/admin/content/gatsby-revision"
 *   },
 *   field_ui_base_route = "entity.gatsby_revision.settings"
 * )
 */
class GatsbyRevision extends ContentEntityBase implements GatsbyRevisionInterface {

  const STATUS_FAILED = 0;
  const STATUS_PASSED = 1;
  const STATUS_IN_PROCESS = 2;

  public static function getStatuses() {
    return [
      self::STATUS_FAILED => t('Failed'),
      self::STATUS_PASSED => t('Passed'),
      self::STATUS_IN_PROCESS => t('In process'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('A friendly reminder of the purpose to the revision.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['gatsby_revision_number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Gatsby Revision number'))
      ->setDescription(t('The number of the revision ID given by the gatsby revision plugin.'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setDescription(t('A description of the gatsby revision.'))
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Revision process results'))
      ->setDescription(t('The status of the process .'))
      ->setDefaultValue(3600)
      ->setSetting('unsigned', TRUE)
      ->setRequired(TRUE)
      ->setSetting('allowed_values', self::getStatuses())
      ->setDisplayOptions('form', [])
      ->setDisplayConfigurable('form', TRUE);

    $fields['error'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Error'))
      ->setDescription(t('Displaying an error in snapshot creation, if any'))
      ->setDisplayOptions('form', [])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the gatsby revision was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
