<?php

namespace Drupal\gatsby_deploy\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\gatsby_deploy\GatsbyDeployInterface;

/**
 * Defines the gatsby deploy entity class.
 *
 * @ContentEntityType(
 *   id = "gatsby_deploy",
 *   label = @Translation("Gatsby deploy"),
 *   label_collection = @Translation("Gatsby deploys"),
 *   label_singular = @Translation("gatsby deploy"),
 *   label_plural = @Translation("gatsby deploys"),
 *   label_count = @PluralTranslation(
 *     singular = "@count gatsby deploys",
 *     plural = "@count gatsby deploys",
 *   ),
 *   handlers = {},
 *   base_table = "gatsby_deploy",
 *   admin_permission = "administer gatsby deploy",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {},
 * )
 */
class GatsbyDeploy extends ContentEntityBase implements GatsbyDeployInterface {

  /**
   * Status for a failed revision build.
   */
  const STATUS_FAILED = 0;

  /**
   * Status for a successful build.
   */
  const STATUS_PASSED = 1;

  /**
   * Get a list of all the state for the gatsby revision build mode.
   *
   * @return array
   *   List of key-label for the status of the revision state.
   */
  public static function getStatuses() {
    return [
      self::STATUS_FAILED => t('Failed'),
      self::STATUS_PASSED => t('Passed'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
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

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the gatsby deploy was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['frontend_environment'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Frontend environment'))
      ->setDescription(t('The environment which triggered the creation.'))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Deploy process status'))
      ->setDescription(t('The status of the process.'))
      ->setDefaultValue(3600)
      ->setSetting('unsigned', TRUE)
      ->setRequired(TRUE)
      ->setSetting('allowed_values', self::getStatuses())
      ->setDisplayOptions('form', [])
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

}
