<?php

namespace Drupal\gatsby_deploy\Plugin\FrontendEnvironment;

use Drupal\build_hooks\BuildHookDetails;
use Drupal\build_hooks\Plugin\FrontendEnvironmentBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\gatsby_deploy\Entity\GatsbyDeploy;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Gatsby plugin trigger deploy' frontend environment type.
 *
 * @FrontendEnvironment(
 *  id = "gatsby-plugin-trigger-deploy",
 *  label = "Gatsby plugin trigger deploy",
 *  description = @translation("Connect Gatsby plugin trigger deploy endpoints")
 * )
 */
class GatsbyPluginTriggerDeploy extends FrontendEnvironmentBase implements ContainerFactoryPluginInterface {

  use MessengerTrait;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, DateFormatter $date_formatter) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function frontEndEnvironmentForm($form, FormStateInterface $form_state) {
    $form['build_hook_url'] = [
      '#type' => 'url',
      '#title' => $this->t('Build hook url'),
      '#maxlength' => 255,
      '#default_value' => isset($this->configuration['build_hook_url']) ? $this->configuration['build_hook_url'] : '',
      '#description' => $this->t("Build hook url for this environment."),
      '#required' => TRUE,
    ];

    $form['secret_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Secret key'),
      '#maxlength' => 255,
      '#default_value' => isset($this->configuration['secret_key']) ? $this->configuration['secret_key'] : '',
      '#description' => $this->t("The secret key which has been set in the gatsby configuration."),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function frontEndEnvironmentSubmit($form, FormStateInterface $form_state) {
    $this->configuration['secret_key'] = $form_state->getValue('secret_key');
    $this->configuration['build_hook_url'] = $form_state->getValue('build_hook_url');
  }

  /**
   * {@inheritdoc}
   */
  public function getBuildHookDetails() {
    $buildHookDetails = new BuildHookDetails();

    $buildHookDetails->setUrl($this->configuration['build_hook_url']);
    $buildHookDetails->setMethod('POST');
    $buildHookDetails->setBody([
      'json' => ['secret_key' => $this->configuration['secret_key']],
    ]);

    return $buildHookDetails;
  }

  /**
   * {@inheritdoc}
   */
  public function getAdditionalDeployFormElements(FormStateInterface $form_state) {
    $form = [];

    $form['recent_deployments'] = [
      '#type' => 'details',
      '#title' => $this->t('Recent deployments'),
      '#description' => $this->t('Here you can see the details for the latest deployments for this environment.'),
      '#open' => TRUE,
      'table' => $this->getDeploysTable(),
    ];

    return $form;
  }

  /**
   * Get the table of the deploys.
   *
   * @return array
   *   The table markup.
   */
  public function getDeploysTable() {
    $element = [
      '#type' => 'table',
      '#header' => [
        $this->t('Status'),
        $this->t('Created at'),
      ],
    ];

    $deployments_ids = $this
      ->entityTypeManager
      ->getStorage('gatsby_deploy')
      ->getQuery()
      ->condition('frontend_environment', \Drupal::request()->attributes->get('frontend_environment')->id())
      ->sort('created', 'desc')
      ->execute();

    $deployments = $this->entityTypeManager->getStorage('gatsby_deploy')->loadMultiple($deployments_ids);

    foreach ($deployments as $index => $deployment) {
      $element[$index] = [
        'created' => [
          '#type' => 'item',
          '#markup' => $this->dateFormatter->format($deployment->get('created')->value),
        ],
        'status' => [
          '#type' => 'item',
          '#markup' => $deployment->get('status')->value == GatsbyDeploy::STATUS_PASSED ? $this->t('Created successfully') : $this->t('Failed, please check the logs'),
        ],
      ];
    }

    return $element;
  }

}
