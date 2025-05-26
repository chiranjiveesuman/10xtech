<?php

namespace Drupal\document_approval\Service;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Service for handling document approval operations.
 */
class DocumentApprovalService extends ControllerBase {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a DocumentApprovalService object.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    AccountProxyInterface $current_user,
    MessengerInterface $messenger
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
    $this->messenger = $messenger;
  }

  /**
   * Creates a new document submission.
   */
  public function createSubmission($files) {
    try {
      // Check if user already has a submission
      $existing = $this->getUserCurrentSubmission($this->currentUser->id());
      if ($existing) {
        $this->messenger->addError($this->t('You already have a submission. Please update your existing submission instead.'));
        return FALSE;
      }

      $submission = $this->entityTypeManager->getStorage('document_submission')->create([
        'user_id' => $this->currentUser->id(),
        'status' => 'pending',
      ]);

      foreach ($files as $key => $file_id) {
        $submission->set($key, $file_id);
        // Set initial status for each document
        $submission->set($key . '_status', 'pending');
      }

      $submission->save();
      \Drupal::logger('document_approval')->info('New submission created for user @uid', ['@uid' => $this->currentUser->id()]);
      return $submission;
    }
    catch (\Exception $e) {
      \Drupal::logger('document_approval')->error('Error creating submission: @error', ['@error' => $e->getMessage()]);
      $this->messenger->addError($this->t('Error creating submission: @error', ['@error' => $e->getMessage()]));
      return FALSE;
    }
  }

  /**
   * Updates document approval status.
   */
  public function updateDocumentStatus($submission_id, $document_field, $status) {
    try {
      $submission = $this->entityTypeManager->getStorage('document_submission')->load($submission_id);
      if (!$submission) {
        throw new \Exception('Submission not found');
      }

      $submission->set($document_field . '_status', $status);
      $submission->save();

      // Check if all mandatory documents (doc1, doc2, doc3) are approved
      $mandatory_approved_count = 0;
      $has_mandatory_files = TRUE;
      $any_mandatory_rejected = FALSE;

      foreach (['doc1', 'doc2', 'doc3'] as $doc) {
        $file = $submission->get($doc)->entity;
        if (!$file) {
          $has_mandatory_files = FALSE;
          break;
        }

        $doc_status = $submission->get($doc . '_status')->value;
        if ($doc_status === 'approved') {
          $mandatory_approved_count++;
        } elseif ($doc_status === 'rejected') {
          $any_mandatory_rejected = TRUE;
        }
      }

      // Update overall submission status based on mandatory documents
      if ($any_mandatory_rejected) {
        $submission->setStatus('rejected');
      } elseif ($has_mandatory_files && $mandatory_approved_count === 3) {
        // All 3 mandatory documents are approved
        $submission->setStatus('approved');
      } else {
        $submission->setStatus('pending');
      }

      $submission->save();

      // Invalidate cache for the user's status page and admin views
      $user = $submission->getUser();
      if ($user) {
        $this->invalidateUserStatusCache($user->id(), $submission->id());
        // Also invalidate admin submission list cache
        $this->invalidateAdminCache();
      }

      return TRUE;
    }
    catch (\Exception $e) {
      $this->messenger->addError($this->t('Error updating document status: @error', ['@error' => $e->getMessage()]));
      return FALSE;
    }
  }

  /**
   * Gets submissions for the current user.
   */
  public function getUserSubmissions() {
    try {
      return $this->entityTypeManager->getStorage('document_submission')
        ->loadByProperties(['user_id' => $this->currentUser->id()]);
    }
    catch (\Exception $e) {
      $this->messenger->addError($this->t('Error loading submissions: @error', ['@error' => $e->getMessage()]));
      return [];
    }
  }

  /**
   * Gets all submissions (for admin).
   */
  public function getAllSubmissions() {
    try {
      return $this->entityTypeManager->getStorage('document_submission')
        ->loadMultiple();
    }
    catch (\Exception $e) {
      $this->messenger->addError($this->t('Error loading submissions: @error', ['@error' => $e->getMessage()]));
      return [];
    }
  }

  /**
   * Gets the current submission for a user.
   *
   * @param int $user_id
   *   The user ID.
   *
   * @return \Drupal\document_approval\Entity\DocumentSubmission|null
   *   The user's current submission, or NULL if none exists.
   */
  public function getUserCurrentSubmission($user_id) {
    try {
      $submissions = $this->entityTypeManager->getStorage('document_submission')
        ->loadByProperties([
          'user_id' => $user_id,
        ]);

      // Return the most recent submission
      return reset($submissions);
    }
    catch (\Exception $e) {
      $this->messenger->addError($this->t('Error loading submission: @error', [
        '@error' => $e->getMessage(),
      ]));
      return NULL;
    }
  }

  /**
   * Gets a specific submission by ID.
   *
   * @param int $submission_id
   *   The submission ID.
   *
   * @return \Drupal\document_approval\Entity\DocumentSubmission|null
   *   The submission entity or NULL if not found.
   */
  public function getSubmission($submission_id) {
    try {
      return $this->entityTypeManager->getStorage('document_submission')->load($submission_id);
    }
    catch (\Exception $e) {
      $this->messenger->addError($this->t('Error loading submission: @error', [
        '@error' => $e->getMessage(),
      ]));
      return NULL;
    }
  }

  /**
   * Invalidates cache for a user's status page.
   *
   * @param int $user_id
   *   The user ID.
   * @param int $submission_id
   *   The submission ID.
   */
  public function invalidateUserStatusCache($user_id, $submission_id) {
    $cache_tags = [
      'document_submission:' . $submission_id,
      'user:' . $user_id . ':document_status',
    ];
    \Drupal::service('cache_tags.invalidator')->invalidateTags($cache_tags);
  }

  /**
   * Invalidates cache for admin views.
   */
  public function invalidateAdminCache() {
    // Clear entity cache first
    \Drupal::entityTypeManager()->getStorage('document_submission')->resetCache();
    \Drupal::service('cache.entity')->deleteAll();

    $cache_tags = [
      'document_submission_list',
      'document_approval_admin',
      'rendered', // Clear all rendered cache
    ];
    \Drupal::service('cache_tags.invalidator')->invalidateTags($cache_tags);

    // Also clear the render cache to ensure admin sees updated files
    \Drupal::service('cache.render')->deleteAll();
  }

}
