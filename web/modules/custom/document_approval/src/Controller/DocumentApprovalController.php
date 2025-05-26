<?php

namespace Drupal\document_approval\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\document_approval\Service\DocumentApprovalService;
use Drupal\Core\Url;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DateFormatterInterface;

/**
 * Controller for document approval functionality.
 */
class DocumentApprovalController extends ControllerBase {

  /**
   * The document approval service.
   *
   * @var \Drupal\document_approval\Service\DocumentApprovalService
   */
  protected $documentService;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * DocumentApprovalController constructor.
   *
   * @param \Drupal\document_approval\Service\DocumentApprovalService $document_service
   *   The document approval service.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(DocumentApprovalService $document_service, DateFormatterInterface $date_formatter) {
    $this->documentService = $document_service;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('document_approval.document_service'),
      $container->get('date.formatter')
    );
  }

  /**
   * Displays the application page for authenticated users.
   *
   * @return array
   *   A render array for the application page.
   */
  public function applicationPage() {
    $user = $this->currentUser();

    // Ensure user is authenticated and has permission
    if (!$user->isAuthenticated()) {
      return $this->redirect('user.login');
    }

    // Check if user has permission to submit documents
    if (!$user->hasPermission('submit documents')) {
      throw new AccessDeniedHttpException();
    }

    // Get user's current submission if it exists
    $submission = $this->documentService->getUserCurrentSubmission($user->id());

    // Get the submission form
    $form = $this->formBuilder()->getForm('Drupal\document_approval\Form\UserDocumentSubmissionForm', $submission);

    // Build the page - the form now handles its own title
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['document-approval-wrapper']],
      'form' => $form,
    ];

    $build['#attached']['library'][] = 'document_approval/application';

    return $build;
  }

  /**
   * Displays a list of all document submissions.
   *
   * @return array
   *   A render array representing the submissions list page.
   */
  public function submissionsList() {
    $submissions = $this->documentService->getAllSubmissions();

    $header = [
      'user' => $this->t('User'),
      'status' => $this->t('Status'),
      'created' => $this->t('Submitted'),
      'operations' => $this->t('Operations'),
    ];

    $rows = [];
    foreach ($submissions as $submission) {
      $user = $submission->getUser();
      $rows[] = [
        'user' => $user ? $user->getAccountName() : $this->t('Unknown'),
        'status' => $submission->getStatus(),
        'created' => $this->dateFormatter->format($submission->get('created')->value, 'short'),
        'operations' => [
          'data' => [
            '#type' => 'operations',
            '#links' => [
              'view' => [
                'title' => $this->t('Review'),
                'url' => Url::fromRoute('document_approval.submission_review', ['submission_id' => $submission->id()]),
              ],
            ],
          ],
        ],
      ];
    }

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No submissions found.'),
    ];

    return $build;
  }



  /**
   * Formats the status for display.
   *
   * @param string $status
   *   The status to format.
   *
   * @return string
   *   The formatted status.
   */
  protected function formatStatus($status) {
    $statuses = [
      'pending' => $this->t('Pending'),
      'approved' => $this->t('Approved'),
      'rejected' => $this->t('Rejected'),
    ];

    return isset($statuses[$status]) ? $statuses[$status] : $status;
  }

  /**
   * Checks if the submission has any editable documents.
   *
   * @param \Drupal\document_approval\Entity\DocumentSubmission $submission
   *   The submission to check.
   *
   * @return bool
   *   TRUE if there are editable documents, FALSE otherwise.
   */
  protected function hasEditableDocuments($submission) {
    for ($i = 1; $i <= 4; $i++) {
      $status = $submission->get("doc{$i}_status")->value;
      if ($status === 'pending' || $status === 'rejected') {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Access callback for the application page.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access() {
    $user = $this->currentUser();
    return AccessResult::allowedIf($user->isAuthenticated() && $user->hasPermission('submit documents'));
  }

}
