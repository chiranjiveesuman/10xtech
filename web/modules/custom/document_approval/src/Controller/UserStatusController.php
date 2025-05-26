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
          '@url' => \Drupal\Core\Url::fromRoute('document_approval.user_submission')->toString(),
        ]),
      ];
    }

    $latest_submission = reset($submissions);
    $status = $latest_submission->getStatus();

    $build = [
      '#type' => 'container',
      'status' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $this->t('Application Status: @status', ['@status' => ucfirst($status)]),
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

    // Add resubmit option if any document is rejected
    if ($status === 'rejected') {
      $build['resubmit'] = [
        '#type' => 'link',
        '#title' => $this->t('Update Documents'),
        '#url' => \Drupal\Core\Url::fromRoute('document_approval.user_submission'),
        '#attributes' => [
          'class' => ['button', 'button--primary'],
        ],
      ];
    }

    return $build;
  }
}
