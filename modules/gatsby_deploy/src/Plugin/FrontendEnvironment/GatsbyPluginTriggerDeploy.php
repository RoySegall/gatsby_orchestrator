<?php

namespace Drupal\gatsby_deploy\Plugin\FrontendEnvironment;

use Drupal\build_hooks\BuildHookDetails;
use Drupal\build_hooks\Plugin\FrontendEnvironmentBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerTrait;

/**
 * Provides a 'Gatsby plugin trigger deploy' frontend environment type.
 *
 * @FrontendEnvironment(
 *  id = "gatsby-plugin-trigger-deploy",
 *  label = "Gatsby plugin trigger deploy",
 *  description = @translation("Connect Gatsby plugin trigger deploy endpoints")
 * )
 */
class GatsbyPluginTriggerDeploy extends FrontendEnvironmentBase {

  use MessengerTrait;

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

    // This plugin adds to the deployment form a fieldset displaying the
    // latest deployments:
    $form = [];

    return $form;
  }

}
