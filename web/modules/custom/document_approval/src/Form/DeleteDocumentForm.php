<?php

namespace Drupal\document_approval\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\document_approval\Service\DocumentApprovalService;

/**
 * Form for deleting a document from a submission.
 */
class DeleteDocumentForm extends ConfirmFormBase {

  /**
   * The document approval service.
   *
   * @var \Drupal\document_approval\Service\DocumentApprovalService
   */
  protected $documentService;

  /**
   * The submission ID.
   *
   * @var int
   */
  protected $submissionId;

  /**
   * The document ID.
   *
   * @var string
   */
  protected $docId;

  /**
   * Constructs a new DeleteDocumentForm.
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
    return 'document_approval_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $doc_id = NULL, $submission_id = NULL) {
    $this->docId = $doc_id;
    $this->submissionId = $submission_id;

    $submission = $this->documentService->getSubmission($submission_id);
    if (!$submission || $submission->getUser()->id() !== $this->currentUser()->id()) {
      $this->messenger()->addError($this->t('Invalid submission.'));
      return $this->redirect('document_approval.application');
    }

    $config = $this->config('document_approval.settings');
    $doc_label = $config->get("{$doc_id}_label") ?: ucfirst(str_replace('_', ' ', $doc_id));

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    $config = $this->config('document_approval.settings');
    $doc_label = $config->get("{$this->docId}_label") ?: ucfirst(str_replace('_', ' ', $this->docId));
    return $this->t('Are you sure you want to delete @doc?', ['@doc' => $doc_label]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('document_approval.application');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $submission = $this->documentService->getSubmission($this->submissionId);
    if ($submission && $submission->getUser()->id() === $this->currentUser()->id()) {
      // Delete the file
      $file = $submission->get($this->docId)->entity;
      if ($file) {
        $file->delete();
      }

      // Clear the field and reset status
      $submission->set($this->docId, NULL);
      $submission->set("{$this->docId}_status", 'pending');
      $submission->save();

      $this->messenger()->addMessage($this->t('Document deleted successfully.'));
    }

    $form_state->setRedirect('document_approval.application');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('This action cannot be undone. You will need to upload a new document.');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete Document');
  }
}
