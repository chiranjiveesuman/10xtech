<?php

namespace Drupal\document_approval\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\document_approval\Service\DocumentApprovalService;

/**
 * Controller for user document status functionality.
 */
class UserStatusController extends ControllerBase {

  /**
   * The document approval service.
   *
   * @var \Drupal\document_approval\Service\DocumentApprovalService
   */
  protected $documentService;

  /**
   * UserStatusController constructor.
   *
   * @param \Drupal\document_approval\Service\DocumentApprovalService $document_service
   *   The document approval service.
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
   * Displays the status of user's document submission.
   *
   * @return array
   *   A render array representing the status page.
   */
  public function status() {
    $submissions = $this->documentService->getUserSubmissions();

    if (empty($submissions)) {
      return [
        '#type' => 'markup',
        '#markup' => $this->t('No document submissions found. <a href="@url">Submit documents</a>.', [
          '@url' => \Drupal\Core\Url::fromRoute('document_approval.application')->toString(),
        ]),
        '#cache' => [
          'max-age' => 0,
          'contexts' => ['user'],
        ],
      ];
    }

    $latest_submission = reset($submissions);
    $status = $latest_submission->getStatus();
    $current_user = $this->currentUser();

    $build = [
      '#type' => 'container',
      'status' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Application Status: @status', ['@status' => ucfirst($status)]),
      ],
      // Disable caching to ensure real-time updates
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['user'],
        'tags' => [
          'document_submission:' . $latest_submission->id(),
          'user:' . $current_user->id() . ':document_status',
        ],
      ],
    ];

    // Display document statuses
    $build['documents'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Document'),
        $this->t('Status'),
      ],
      '#rows' => [],
    ];

    $config = $this->config('document_approval.settings');
    for ($i = 1; $i <= 4; $i++) {
      $doc = "doc{$i}";
      $label = $config->get("{$doc}_label") ?: "Document {$i}";
      $doc_status = $latest_submission->get("{$doc}_status")->value ?: 'pending';

      if ($latest_submission->get($doc)->target_id || $i < 4) {
        $build['documents']['#rows'][] = [
          $label,
          ucfirst($doc_status),
        ];
      }
    }

    // Check if all mandatory documents are approved
    $all_mandatory_approved = TRUE;
    for ($i = 1; $i <= 3; $i++) {
      $doc = "doc{$i}";
      $current_file = $latest_submission->get($doc)->entity;
      $doc_status = $latest_submission->get("{$doc}_status")->value ?: 'pending';

      if (!$current_file || $doc_status !== 'approved') {
        $all_mandatory_approved = FALSE;
        break;
      }
    }

    // Add resubmit option if any document is rejected
    if ($status === 'rejected') {
      $build['resubmit'] = [
        '#type' => 'link',
        '#title' => $this->t('Update Documents'),
        '#url' => \Drupal\Core\Url::fromRoute('document_approval.application'),
        '#attributes' => [
          'class' => ['button', 'button--primary'],
        ],
      ];
    }

    // Show congratulations message if all mandatory documents are approved
    if ($all_mandatory_approved) {
      $build['congratulations'] = [
        '#type' => 'item',
        '#markup' => '<div class="messages messages--status congratulations-message">' .
                     $this->t('🎉 Congratulations! All your mandatory documents have been approved. Your application is complete!') .
                     '</div>',
        '#weight' => 5,
      ];

      // Remove auto-refresh when all documents are approved
      unset($build['#attached']['html_head']);
    }

    // Add auto-refresh meta tag for real-time updates
    $build['#attached']['html_head'][] = [
      [
        '#tag' => 'meta',
        '#attributes' => [
          'http-equiv' => 'refresh',
          'content' => '30', // Refresh every 30 seconds
        ],
      ],
      'auto_refresh',
    ];

    return $build;
  }
}
