<?php

namespace Drupal\document_approval\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\document_approval\Service\DocumentApprovalService;
use Drupal\Core\StringTranslation\ByteSizeMarkup;

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
    // Clear all caches to ensure we get the latest data
    \Drupal::entityTypeManager()->getStorage('document_submission')->resetCache();
    \Drupal::service('cache.entity')->deleteAll();
    \Drupal::service('cache.render')->deleteAll();

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

      // Always show doc4 section if user has ever uploaded it (check if status exists)
      $doc_status = $submission->get("{$doc}_status")->value;
      if (!$file && $i >= 4 && !$doc_status) {
        continue; // Skip optional document only if never uploaded
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
          // For PDF files, provide multiple viewing options
          $form[$doc]['preview'] = [
            '#type' => 'item',
            '#title' => $this->t('Document Preview'),
            '#markup' => '
              <div class="admin-document-preview">
                <div class="pdf-preview-container">
                  <iframe src="' . $file_url . '#toolbar=1&navpanes=1&scrollbar=1"
                          width="100%"
                          height="600px"
                          style="border: 1px solid #ccc; border-radius: 4px;"
                          title="PDF Preview">
                    <p>Your browser does not support iframes. <a href="' . $file_url . '" target="_blank">Click here to view the PDF</a></p>
                  </iframe>
                </div>
                <div class="pdf-actions" style="margin-top: 10px; text-align: center;">
                  <a href="' . $file_url . '" target="_blank" class="button button--primary" style="margin-right: 10px;">
                    📄 Open in New Tab
                  </a>
                  <a href="' . $file_url . '" download class="button">
                    💾 Download PDF
                  </a>
                </div>
              </div>',
          ];
        } elseif (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif'])) {
          // For image files, display directly with zoom functionality
          $form[$doc]['preview'] = [
            '#type' => 'item',
            '#title' => $this->t('Document Preview'),
            '#markup' => '
              <div class="admin-document-preview">
                <div class="image-preview-container" style="text-align: center;">
                  <a href="' . $file_url . '" target="_blank">
                    <img src="' . $file_url . '"
                         style="max-width: 100%; max-height: 500px; height: auto; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;"
                         alt="' . $file->getFilename() . '"
                         title="Click to view full size">
                  </a>
                </div>
                <div class="image-actions" style="margin-top: 10px; text-align: center;">
                  <a href="' . $file_url . '" target="_blank" class="button button--primary" style="margin-right: 10px;">
                    🖼️ View Full Size
                  </a>
                  <a href="' . $file_url . '" download class="button">
                    💾 Download Image
                  </a>
                </div>
              </div>',
          ];
        } else {
          // For other file types, provide download link
          $form[$doc]['preview'] = [
            '#type' => 'item',
            '#title' => $this->t('Document Preview'),
            '#markup' => '
              <div class="admin-document-preview">
                <div class="file-preview-container" style="text-align: center; padding: 2rem; border: 1px solid #ccc; border-radius: 4px; background-color: #f8f9fa;">
                  <div style="font-size: 3rem; margin-bottom: 1rem;">📄</div>
                  <p><strong>' . $file->getFilename() . '</strong></p>
                  <p>File type: ' . strtoupper($file_extension) . '</p>
                  <a href="' . $file_url . '" target="_blank" class="button button--primary" style="margin-right: 10px;">
                    👁️ Open File
                  </a>
                  <a href="' . $file_url . '" download class="button">
                    💾 Download File
                  </a>
                </div>
              </div>',
          ];
        }

        $form[$doc]['file_info'] = [
          '#type' => 'item',
          '#title' => $this->t('File Information'),
          '#markup' => $this->t('Filename: @name<br>Size: @size', [
            '@name' => $file->getFilename(),
            '@size' => ByteSizeMarkup::create($file->getSize()),
          ]),
        ];
      }
      else {
        $form[$doc]['preview'] = [
          '#type' => 'item',
          '#markup' => $this->t('No document uploaded.'),
        ];
      }

      // If document is already approved, show read-only status instead of radio buttons
      if ($current_status === 'approved') {
        $form[$doc]["status_{$doc}"] = [
          '#type' => 'item',
          '#title' => $this->t('Approval Status'),
          '#markup' => '<span class="status-approved"><strong>' . $this->t('Approved') . '</strong></span>',
        ];

        // Add a hidden field to maintain the approved status
        $form[$doc]["status_{$doc}_hidden"] = [
          '#type' => 'hidden',
          '#value' => 'approved',
        ];

        // Show comments as read-only if they exist
        $comments = $submission->get("{$doc}_comments")->value;
        if ($comments) {
          $form[$doc]["comments_{$doc}"] = [
            '#type' => 'item',
            '#title' => $this->t('Comments'),
            '#markup' => '<div class="admin-comments">' . $comments . '</div>',
          ];
        }
      } else {
        // Show normal radio buttons for pending/rejected documents
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
    }

    // Check if all mandatory documents are approved
    $all_mandatory_approved = TRUE;
    $has_pending_or_rejected = FALSE;
    $has_editable_documents = FALSE;

    for ($i = 1; $i <= 3; $i++) { // Only check mandatory documents (1, 2, 3)
      $doc = "doc{$i}";
      $file = $submission->get($doc)->entity;
      if ($file) {
        $doc_status = $submission->get("{$doc}_status")->value ?: 'pending';
        if ($doc_status !== 'approved') {
          $all_mandatory_approved = FALSE;
          if ($doc_status === 'pending' || $doc_status === 'rejected') {
            $has_pending_or_rejected = TRUE;
          }
        }
      } else {
        // If mandatory document is missing, we need the form
        $all_mandatory_approved = FALSE;
        $has_pending_or_rejected = TRUE;
      }
    }

    // Also check non-mandatory documents (doc4) for editable status
    for ($i = 4; $i <= 4; $i++) {
      $doc = "doc{$i}";
      $file = $submission->get($doc)->entity;
      $doc_status = $submission->get("{$doc}_status")->value;

      // If doc4 exists and is not approved, it's editable
      if ($file && $doc_status && $doc_status !== 'approved') {
        $has_editable_documents = TRUE;
      }
    }

    // Show submit button if there are documents that need review (mandatory or non-mandatory)
    if ($has_pending_or_rejected || !$all_mandatory_approved || $has_editable_documents) {
      $form['actions'] = [
        '#type' => 'actions',
        'submit' => [
          '#type' => 'submit',
          '#value' => $this->t('Save Review'),
          '#button_type' => 'primary',
        ],
      ];
    } else {
      // Show a message instead of the submit button
      $form['completion_message'] = [
        '#type' => 'item',
        '#markup' => '<div class="messages messages--status">' .
                     $this->t('All mandatory documents have been approved. No further action is required.') .
                     '</div>',
        '#weight' => 100,
      ];
    }

    // Disable caching to ensure real-time updates
    $form['#cache'] = [
      'max-age' => 0,
      'contexts' => ['user.permissions'],
      'tags' => [
        'document_submission:' . $submission->id(),
        'document_approval_admin',
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

    // Check if all mandatory documents are already approved
    $all_mandatory_approved = TRUE;
    for ($i = 1; $i <= 3; $i++) {
      $doc = "doc{$i}";
      $file = $submission->get($doc)->entity;
      if ($file) {
        $doc_status = $submission->get("{$doc}_status")->value ?: 'pending';
        if ($doc_status !== 'approved') {
          $all_mandatory_approved = FALSE;
          break;
        }
      } else {
        $all_mandatory_approved = FALSE;
        break;
      }
    }

    // Check if there are any non-mandatory documents that need processing
    $has_non_mandatory_changes = FALSE;
    for ($i = 4; $i <= 4; $i++) {
      $doc = "doc{$i}";
      $file = $submission->get($doc)->entity;
      $doc_status = $submission->get("{$doc}_status")->value;

      // If doc4 exists and has a status that can be changed
      if ($file && $doc_status && $doc_status !== 'approved') {
        $form_status = $form_state->getValue("status_{$doc}");
        // Check if the status is being changed
        if ($form_status && $form_status !== $doc_status) {
          $has_non_mandatory_changes = TRUE;
          break;
        }
      }
    }

    // If all mandatory documents are approved AND no non-mandatory changes, don't process
    if ($all_mandatory_approved && !$has_non_mandatory_changes) {
      $this->messenger()->addMessage($this->t('All mandatory documents are already approved and no changes were made to optional documents.'));
      $form_state->setRedirect('document_approval.admin_submissions');
      return;
    }

    // Track status for mandatory documents only (doc1, doc2, doc3)
    $mandatory_approved_count = 0;
    $any_rejected = FALSE;
    $has_mandatory_files = TRUE;

    // Update status for each document
    for ($i = 1; $i <= 4; $i++) {
      $doc = "doc{$i}";
      if ($submission->hasField($doc) && ($submission->get($doc)->target_id || $i < 4)) {
        // Check if this document was already approved (using hidden field)
        $hidden_status = $form_state->getValue("status_{$doc}_hidden");
        if ($hidden_status === 'approved') {
          // Document is already approved, don't change its status
          $status = 'approved';
          $comments = $submission->get("{$doc}_comments")->value; // Keep existing comments
        } else {
          // Document is not approved, get values from form
          $status = $form_state->getValue("status_{$doc}");
          $comments = $form_state->getValue("comments_{$doc}");

          // Only update if status or comments changed
          $submission->set("{$doc}_status", $status);
          $submission->set("{$doc}_comments", $comments);
        }

        // Track mandatory document statuses (doc1, doc2, doc3)
        if ($i <= 3) {
          $file = $submission->get($doc)->entity;
          if (!$file) {
            $has_mandatory_files = FALSE;
          } elseif ($status === 'approved') {
            $mandatory_approved_count++;
          } elseif ($status === 'rejected') {
            // Only track rejections for mandatory documents
            $any_rejected = TRUE;
          }
        }
      }
    }

    // Update overall submission status based on mandatory documents
    if ($any_rejected) {
      $submission->setStatus('rejected');
    }
    elseif ($has_mandatory_files && $mandatory_approved_count === 3) {
      // All 3 mandatory documents are approved
      $submission->setStatus('approved');
    }
    else {
      $submission->setStatus('pending');
    }

    $submission->save();

    // Invalidate cache for the user's status page and admin views
    $user = $submission->getUser();
    if ($user) {
      $cache_tags = [
        'document_submission:' . $submission->id(),
        'user:' . $user->id() . ':document_status',
        'document_submission_list',
        'document_approval_admin',
        'rendered', // Clear all rendered cache
      ];
      \Drupal::service('cache_tags.invalidator')->invalidateTags($cache_tags);

      // Also clear the cache for the specific admin review page
      \Drupal::service('cache.render')->deleteAll();
    }

    $this->messenger()->addMessage($this->t('Document review has been saved.'));
    $form_state->setRedirect('document_approval.admin_submissions');
  }
}
