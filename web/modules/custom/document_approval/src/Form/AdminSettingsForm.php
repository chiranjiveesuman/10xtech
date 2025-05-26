<?php

namespace Drupal\document_approval\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a configuration form for document approval settings.
 */
class AdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'document_approval_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['document_approval.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('document_approval.settings');

    $form['document_labels'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Document Labels'),
    ];

    for ($i = 1; $i <= 4; $i++) {
      $form['document_labels']["doc{$i}_label"] = [
        '#type' => 'textfield',
        '#title' => $this->t('Document @num Label', ['@num' => $i]),
        '#default_value' => $config->get("doc{$i}_label") ?: "Document {$i}",
        '#required' => $i < 4,
      ];
    }

    $form['file_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('File Settings'),
    ];

    $form['file_settings']['allowed_extensions'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Allowed File Extensions'),
      '#default_value' => $config->get('allowed_extensions') ?: 'pdf jpg jpeg',
      '#description' => $this->t('Separate extensions with a space'),
      '#required' => TRUE,
    ];

    $form['file_settings']['max_filesize'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Maximum File Size'),
      '#default_value' => $config->get('max_filesize') ?: '10 MB',
      '#description' => $this->t('Enter the maximum file size with unit (e.g., 10 MB)'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('document_approval.settings');
    
    // Save document labels
    for ($i = 1; $i <= 4; $i++) {
      $config->set("doc{$i}_label", $form_state->getValue("doc{$i}_label"));
    }

    // Save file settings
    $config->set('allowed_extensions', $form_state->getValue('allowed_extensions'))
      ->set('max_filesize', $form_state->getValue('max_filesize'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
