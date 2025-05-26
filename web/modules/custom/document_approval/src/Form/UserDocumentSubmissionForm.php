<?php

namespace Drupal\document_approval\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\document_approval\Service\DocumentApprovalService;
use Drupal\file\Entity\File;

/**
 * Provides a form for users to submit documents.
 */
class UserDocumentSubmissionForm extends FormBase {

  /**
   * The document approval service.
   *
   * @var \Drupal\document_approval\Service\DocumentApprovalService
   */
  protected $documentService;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Constructs a UserDocumentSubmissionForm object.
   */
  public function __construct(
    DocumentApprovalService $document_service,
    FileSystemInterface $file_system
  ) {
    $this->documentService = $document_service;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('document_approval.document_service'),
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'document_approval_submission_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $submission = NULL) {
    $config = $this->config('document_approval.settings');
    $user = $this->currentUser();

    // Check if user already has a submission
    if (!$submission) {
      $submission = $this->documentService->getUserCurrentSubmission($user->id());
    }

    $form['#attributes']['enctype'] = 'multipart/form-data';

    // Add page title based on submission status
    if ($submission) {
      $form['page_title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => $this->t('Application Status'),
        '#weight' => -10,
      ];

      $form['current_status'] = [
        '#type' => 'item',
        '#title' => $this->t('Current Status'),
        '#markup' => '<strong>' . ucfirst($submission->getStatus()) . '</strong>',
        '#weight' => -9,
      ];
    } else {
      $form['page_title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h1',
        '#value' => $this->t('Document Registration'),
        '#weight' => -10,
      ];
    }

    $form['documents'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Required Documents'),
      '#weight' => 0,
    ];

    for ($i = 1; $i <= 4; $i++) {
      $required = $i < 4;
      $label = $config->get("doc{$i}_label") ?: "Document {$i}";

      // Check if this document exists and its status
      $current_file = NULL;
      $doc_status = 'pending';
      if ($submission) {
        $current_file = $submission->get("doc{$i}")->entity;
        $doc_status = $submission->get("doc{$i}_status")->value ?: 'pending';
      }

      $form['documents']["doc{$i}"] = [
        '#type' => 'managed_file',
        '#title' => $label . ($required ? ' *' : ' (Optional)'),
        '#required' => $required && !$current_file,
        '#upload_location' => 'public://document_submissions/' . date('Y-m'),
        '#upload_validators' => [
          'file_validate_extensions' => [$config->get('allowed_extensions') ?: 'pdf jpg jpeg'],
          'file_validate_size' => [$this->parseFileSize($config->get('max_filesize') ?: '10 MB')],
        ],
        '#description' => $this->t('Allowed extensions: @ext. Maximum size: @size', [
          '@ext' => $config->get('allowed_extensions') ?: 'pdf jpg jpeg',
          '@size' => $config->get('max_filesize') ?: '10 MB',
        ]),
      ];

      // Set default value if file exists
      if ($current_file) {
        $form['documents']["doc{$i}"]['#default_value'] = [$current_file->id()];
      }

      // Show current file status if exists
      if ($current_file) {
        $status_class = $doc_status === 'approved' ? 'status-approved' : ($doc_status === 'rejected' ? 'status-rejected' : 'status-pending');
        $form['documents']["doc{$i}_current"] = [
          '#type' => 'item',
          '#title' => $this->t('Current File'),
          '#markup' => $this->t('File: @name<br>Status: <span class="@class">@status</span>', [
            '@name' => $current_file->getFilename(),
            '@status' => ucfirst($doc_status),
            '@class' => $status_class,
          ]),
        ];

        // Show comments if rejected
        if ($doc_status === 'rejected') {
          $comments = $submission->get("doc{$i}_comments")->value;
          if ($comments) {
            $form['documents']["doc{$i}_comments"] = [
              '#type' => 'item',
              '#title' => $this->t('Admin Comments'),
              '#markup' => '<div class="rejection-comments">' . $comments . '</div>',
            ];
          }
        }
      }
    }

    $form['actions'] = [
      '#type' => 'actions',
      '#weight' => 10,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $submission ? $this->t('Update Documents') : $this->t('Submit Documents'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Get existing submission to check for already uploaded files
    $user = $this->currentUser();
    $submission = $this->documentService->getUserCurrentSubmission($user->id());

    // Debug: Log submission status
    \Drupal::logger('document_approval')->info('Validation - User ID: @uid, Submission found: @found', [
      '@uid' => $user->id(),
      '@found' => $submission ? 'YES (ID: ' . $submission->id() . ')' : 'NO'
    ]);

    // Debug: Log form values
    $form_values = $form_state->getValues();
    \Drupal::logger('document_approval')->info('Form values: @values', [
      '@values' => print_r($form_values, true)
    ]);

    // Track if any new files are being uploaded
    $has_new_files = false;

    for ($i = 1; $i <= 4; $i++) {
      $required = $i < 4; // Documents 1, 2, 3 are required; 4 is optional

      // Get file from form state - managed_file fields store values directly under the field name
      $file_id = $form_state->getValue("doc{$i}");
      $new_file = $file_id ? reset($file_id) : NULL;

      // Debug: Log file detection
      \Drupal::logger('document_approval')->info('Doc @num - File ID from form: @file', [
        '@num' => $i,
        '@file' => $new_file ? 'YES (ID: ' . $new_file . ')' : 'NO'
      ]);

      // Check if document already exists in the submission
      $existing_file = NULL;
      if ($submission) {
        try {
          $existing_file = $submission->get("doc{$i}")->entity;
          \Drupal::logger('document_approval')->info('Doc @num - Existing file: @file', [
            '@num' => $i,
            '@file' => $existing_file ? 'YES (ID: ' . $existing_file->id() . ')' : 'NO'
          ]);
        } catch (\Exception $e) {
          \Drupal::logger('document_approval')->info('Doc @num - Error getting file: @error', [
            '@num' => $i,
            '@error' => $e->getMessage()
          ]);
        }
      }

      // Track if we have new files
      if ($new_file) {
        $has_new_files = true;
      }

      // For required documents: need either existing file OR new upload
      if ($required) {
        if (!$existing_file && !$new_file) {
          \Drupal::logger('document_approval')->info('Doc @num - VALIDATION ERROR: No existing file and no new upload', ['@num' => $i]);
          $form_state->setError($form['documents']["doc{$i}"], $this->t('Document @num is required.', ['@num' => $i]));
        } else {
          \Drupal::logger('document_approval')->info('Doc @num - VALIDATION OK: Has existing file or new upload', ['@num' => $i]);
        }
      }
    }

    // If no existing submission and no new files uploaded, that's an error
    if (!$submission && !$has_new_files) {
      \Drupal::logger('document_approval')->info('FINAL VALIDATION ERROR: No submission and no new files');
      $form_state->setErrorByName('documents', $this->t('Please upload at least the required documents.'));
    } else {
      \Drupal::logger('document_approval')->info('FINAL VALIDATION OK: Has submission or new files');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = $this->currentUser();
    $submission = $this->documentService->getUserCurrentSubmission($user->id());
    $files = [];

    // Debug: Log that we're in submitForm
    \Drupal::logger('document_approval')->info('SUBMIT FORM - Starting submission process for user @uid', [
      '@uid' => $user->id()
    ]);

    try {
      for ($i = 1; $i <= 4; $i++) {
        // Get file from form state - managed_file fields store values directly under the field name
        $file_id = $form_state->getValue("doc{$i}");
        $new_file = $file_id ? reset($file_id) : NULL;

        // Debug: Log file processing
        \Drupal::logger('document_approval')->info('SUBMIT - Doc @num: File ID = @id', [
          '@num' => $i,
          '@id' => $new_file ? $new_file : 'NULL'
        ]);

        if ($new_file) {
          // Load the file entity and make it permanent
          $file_entity = File::load($new_file);
          if ($file_entity) {
            $file_entity->setPermanent();
            $file_entity->save();
            $files["doc{$i}"] = $file_entity->id();

            \Drupal::logger('document_approval')->info('SUBMIT - Doc @num: File made permanent, ID = @id', [
              '@num' => $i,
              '@id' => $file_entity->id()
            ]);
          } else {
            \Drupal::logger('document_approval')->error('SUBMIT - Doc @num: Could not load file entity with ID @id', [
              '@num' => $i,
              '@id' => $new_file
            ]);
          }
        }
      }

      // Debug: Log files array
      \Drupal::logger('document_approval')->info('SUBMIT - Files to process: @files', [
        '@files' => print_r($files, true)
      ]);

      if ($submission) {
        // Update existing submission
        \Drupal::logger('document_approval')->info('SUBMIT - Updating existing submission ID: @id', [
          '@id' => $submission->id()
        ]);

        foreach ($files as $key => $file_id) {
          $submission->set($key, $file_id);
          // Reset status to pending when document is updated
          $submission->set($key . '_status', 'pending');
          $submission->set($key . '_comments', '');
        }

        // Update overall status to pending if any document was updated
        if (!empty($files)) {
          $submission->setStatus('pending');
        }

        $submission->save();
        \Drupal::logger('document_approval')->info('SUBMIT - Submission updated successfully');
        $this->messenger()->addMessage($this->t('Documents updated successfully.'));
      } else {
        // Create new submission
        \Drupal::logger('document_approval')->info('SUBMIT - Creating new submission');

        if (!empty($files)) {
          if ($this->documentService->createSubmission($files)) {
            \Drupal::logger('document_approval')->info('SUBMIT - New submission created successfully');
            $this->messenger()->addMessage($this->t('Documents submitted successfully.'));
          } else {
            \Drupal::logger('document_approval')->error('SUBMIT - Failed to create submission');
            $this->messenger()->addError($this->t('Failed to create submission.'));
          }
        } else {
          \Drupal::logger('document_approval')->error('SUBMIT - No files were uploaded');
          $this->messenger()->addError($this->t('No files were uploaded.'));
        }
      }

      $form_state->setRedirect('document_approval.user_status');
    } catch (\Exception $e) {
      \Drupal::logger('document_approval')->error('Error in form submission: @error', ['@error' => $e->getMessage()]);
      $this->messenger()->addError($this->t('An error occurred while processing your submission. Please try again.'));
    }
  }

  /**
   * Parse file size string to bytes.
   *
   * @param string $size
   *   File size string like "10 MB".
   *
   * @return int
   *   Size in bytes.
   */
  private function parseFileSize($size) {
    $size = trim($size);
    $unit = strtoupper(substr($size, -2));
    $value = (int) substr($size, 0, -2);

    switch ($unit) {
      case 'KB':
        return $value * 1024;
      case 'MB':
        return $value * 1024 * 1024;
      case 'GB':
        return $value * 1024 * 1024 * 1024;
      default:
        // If no unit specified, assume bytes
        return (int) $size;
    }
  }
}
