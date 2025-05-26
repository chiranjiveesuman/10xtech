<?php

namespace Drupal\document_approval\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\document_approval\Service\DocumentApprovalService;

/**
 * Form for admin to approve or reject submitted documents.
 */
class AdminApprovalForm extends FormBase {

  /**
   * The document approval service.
   *
   * @var \Drupal\document_approval\Service\DocumentApprovalService
   */
  protected $documentService;

  /**
   * Constructs an AdminApprovalForm object.
   */
  public function __construct(DocumentApprovalService $document_service) {
    $this->documentService = $document_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('document_approval.document_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'document_approval_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $submission_id = NULL) {
    $submission = \Drupal::entityTypeManager()
      ->getStorage('document_submission')
      ->load($submission_id);

    if (!$submission) {
      $this->messenger()->addError($this->t('Submission not found.'));
      return $form;
    }

    $form['submission_id'] = [
      '#type' => 'value',
      '#value' => $submission_id,
    ];

    $user = $submission->getUser();
    $form['user_info'] = [
      '#type' => 'item',
      '#title' => $this->t('Submitted by'),
      '#markup' => $user ? $user->getAccountName() : $this->t('Unknown'),
    ];

    $form['documents'] = [
      '#type' => 'vertical_tabs',
      '#title' => $this->t('Document Review'),
    ];

    $config = $this->config('document_approval.settings');

    // Create a tab for each document
    for ($i = 1; $i <= 4; $i++) {
      $doc = "doc{$i}";
      $file = $submission->get($doc)->entity;
      if (!$file && $i >= 4) {
        continue; // Skip optional document if not uploaded
      }

      $label = $config->get("{$doc}_label") ?: "Document {$i}";
      $current_status = $submission->get("{$doc}_status")->value ?: 'pending';

      $form[$doc] = [
        '#type' => 'details',
        '#title' => $label,
        '#group' => 'documents',
      ];

      if ($file) {
        $file_url = \Drupal::service('file_url_generator')->generateAbsoluteString($file->getFileUri());
        $file_extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

        if (in_array(strtolower($file_extension), ['pdf'])) {
          // For PDF files, embed in iframe for viewing
          $form[$doc]['preview'] = [
            '#type' => 'item',
            '#title' => $this->t('Document Preview'),
            '#markup' => '<iframe src="' . $file_url . '" width="100%" height="600px" style="border: 1px solid #ccc;"></iframe>',
          ];
        } else {
          // For image files, display directly
          $form[$doc]['preview'] = [
            '#type' => 'item',
            '#title' => $this->t('Document Preview'),
            '#markup' => '<img src="' . $file_url . '" style="max-width: 100%; height: auto; border: 1px solid #ccc;" alt="' . $file->getFilename() . '">',
          ];
        }

        $form[$doc]['file_info'] = [
          '#type' => 'item',
          '#title' => $this->t('File Information'),
          '#markup' => $this->t('Filename: @name<br>Size: @size', [
            '@name' => $file->getFilename(),
            '@size' => \Drupal::service('file.formatter')->formatSize($file->getSize()),
          ]),
        ];
      }
      else {
        $form[$doc]['preview'] = [
          '#type' => 'item',
          '#markup' => $this->t('No document uploaded.'),
        ];
      }

      $form[$doc]["status_{$doc}"] = [
        '#type' => 'radios',
        '#title' => $this->t('Approval Status'),
        '#options' => [
          'pending' => $this->t('Pending'),
          'approved' => $this->t('Approve'),
          'rejected' => $this->t('Reject'),
        ],
        '#default_value' => $current_status,
        '#required' => TRUE,
      ];

      $form[$doc]["comments_{$doc}"] = [
        '#type' => 'textarea',
        '#title' => $this->t('Comments'),
        '#default_value' => $submission->get("{$doc}_comments")->value,
        '#states' => [
          'visible' => [
            ":input[name=\"status_{$doc}\"]" => ['value' => 'rejected'],
          ],
        ],
      ];
    }

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Save Review'),
        '#button_type' => 'primary',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $submission_id = $form_state->getValue('submission_id');
    $submission = \Drupal::entityTypeManager()
      ->getStorage('document_submission')
      ->load($submission_id);

    if (!$submission) {
      $this->messenger()->addError($this->t('Submission not found.'));
      return;
    }

    $any_rejected = FALSE;
    $all_approved = TRUE;

    // Update status for each document
    for ($i = 1; $i <= 4; $i++) {
      $doc = "doc{$i}";
      if ($submission->hasField($doc) && ($submission->get($doc)->target_id || $i < 4)) {
        $status = $form_state->getValue("status_{$doc}");
        $comments = $form_state->getValue("comments_{$doc}");

        $submission->set("{$doc}_status", $status);
        $submission->set("{$doc}_comments", $comments);

        if ($status === 'rejected') {
          $any_rejected = TRUE;
          $all_approved = FALSE;
        }
        elseif ($status !== 'approved') {
          $all_approved = FALSE;
        }
      }
    }

    // Update overall submission status
    if ($any_rejected) {
      $submission->setStatus('rejected');
    }
    elseif ($all_approved) {
      $submission->setStatus('approved');
    }
    else {
      $submission->setStatus('pending');
    }

    $submission->save();
    $this->messenger()->addMessage($this->t('Document review has been saved.'));
    $form_state->setRedirect('document_approval.admin_submissions');
  }
}
