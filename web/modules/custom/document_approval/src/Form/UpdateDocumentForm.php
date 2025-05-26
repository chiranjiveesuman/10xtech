<?php

namespace Drupal\document_approval\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\document_approval\Service\DocumentApprovalService;

/**
 * Form for updating a document in a submission.
 */
class UpdateDocumentForm extends FormBase {

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
   * Constructs a new UpdateDocumentForm.
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
    return 'document_approval_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $doc_id = NULL, $submission_id = NULL) {
    $submission = $this->documentService->getSubmission($submission_id);
    
    if (!$submission || $submission->getUser()->id() !== $this->currentUser()->id()) {
      $this->messenger()->addError($this->t('Invalid submission.'));
      return $form;
    }

    $config = $this->config('document_approval.settings');
    $doc_label = $config->get("{$doc_id}_label") ?: ucfirst(str_replace('_', ' ', $doc_id));

    $form['submission_id'] = [
      '#type' => 'value',
      '#value' => $submission_id,
    ];

    $form['doc_id'] = [
      '#type' => 'value',
      '#value' => $doc_id,
    ];

    $form['document'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload @doc', ['@doc' => $doc_label]),
      '#required' => TRUE,
      '#description' => $this->t('Allowed extensions: @ext. Maximum size: @size', [
        '@ext' => $config->get('allowed_extensions') ?: 'pdf jpg jpeg',
        '@size' => $config->get('max_filesize') ?: '10 MB',
      ]),
    ];

    if ($submission->get("{$doc_id}_status")->value === 'rejected') {
      $comments = $submission->get("{$doc_id}_comments")->value;
      if ($comments) {
        $form['rejection_reason'] = [
          '#type' => 'item',
          '#title' => $this->t('Rejection Reason'),
          '#markup' => $comments,
          '#prefix' => '<div class="rejection-reason">',
          '#suffix' => '</div>',
        ];
      }
    }

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Update Document'),
      ],
      'cancel' => [
        '#type' => 'link',
        '#title' => $this->t('Cancel'),
        '#url' => \Drupal\Core\Url::fromRoute('document_approval.application'),
        '#attributes' => ['class' => ['button', 'button--danger']],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $file = $this->getRequest()->files->get('files[document]');
    if (!$file) {
      $form_state->setError($form['document'], $this->t('No file was uploaded.'));
      return;
    }

    $config = $this->config('document_approval.settings');
    $allowed_extensions = $config->get('allowed_extensions') ?: 'pdf jpg jpeg';
    $max_filesize = $config->get('max_filesize') ?: '10 MB';

    // Validate file extension
    $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
    if (!in_array($extension, explode(' ', $allowed_extensions))) {
      $form_state->setError($form['document'], $this->t('File @name has an invalid extension.', [
        '@name' => $file->getClientOriginalName(),
      ]));
    }

    // Validate file size
    if ($file->getSize() > \Drupal\Core\File\FileSystem::unformatSize($max_filesize)) {
      $form_state->setError($form['document'], $this->t('File @name is too large.', [
        '@name' => $file->getClientOriginalName(),
      ]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $submission_id = $form_state->getValue('submission_id');
    $doc_id = $form_state->getValue('doc_id');
    $file = $this->getRequest()->files->get('files[document]');

    if ($file) {
      $directory = 'public://document_submissions/' . date('Y-m');
      $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);

      $file_entity = \Drupal::service('file.repository')->writeData(
        file_get_contents($file->getRealPath()),
        "public://document_submissions/" . date('Y-m') . '/' . $file->getClientOriginalName(),
        FileSystemInterface::EXISTS_RENAME
      );

      if ($file_entity) {
        $submission = $this->documentService->getSubmission($submission_id);
        if ($submission) {
          // Remove old file if it exists
          $old_file = $submission->get($doc_id)->entity;
          if ($old_file) {
            $old_file->delete();
          }

          // Update with new file
          $submission->set($doc_id, $file_entity->id());
          $submission->set("{$doc_id}_status", 'pending');
          $submission->save();

          $this->messenger()->addMessage($this->t('Document updated successfully.'));
        }
      }
    }

    $form_state->setRedirect('document_approval.application');
  }
}
