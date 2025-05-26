<?php

namespace Drupal\Tests\document_approval\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\user\Entity\User;
use Drupal\user\Entity\Role;

/**
 * Tests the Document Approval module functionality.
 *
 * @group document_approval
 */
class DocumentApprovalTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['document_approval', 'file', 'user'];

  /**
   * A user with permission to submit documents.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $submitterUser;

  /**
   * A user with permission to administer document approval.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create roles and permissions.
    $submitter_role = Role::create([
      'id' => 'document_submitter',
      'label' => 'Document Submitter',
    ]);
    $submitter_role->grantPermission('submit documents');
    $submitter_role->save();

    $admin_role = Role::create([
      'id' => 'document_admin',
      'label' => 'Document Administrator',
    ]);
    $admin_role->grantPermission('administer document approval');
    $admin_role->grantPermission('access administration pages');
    $admin_role->save();

    // Create users.
    $this->submitterUser = $this->drupalCreateUser();
    $this->submitterUser->addRole('document_submitter');
    $this->submitterUser->save();

    $this->adminUser = $this->drupalCreateUser();
    $this->adminUser->addRole('document_admin');
    $this->adminUser->save();
  }

  /**
   * Tests the admin settings form.
   */
  public function testAdminSettings() {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/config/document-approval/settings');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Document Approval Settings');
    
    // Test form submission.
    $edit = [
      'doc1_label' => 'Test Document 1',
      'doc2_label' => 'Test Document 2',
      'doc3_label' => 'Test Document 3',
      'doc4_label' => 'Test Document 4',
      'allowed_extensions' => 'pdf jpg jpeg',
      'max_filesize' => '5 MB',
    ];
    $this->submitForm($edit, 'Save configuration');
    $this->assertSession()->pageTextContains('The configuration options have been saved.');
  }

  /**
   * Tests the application page access.
   */
  public function testApplicationPageAccess() {
    // Test anonymous user access.
    $this->drupalGet('/application');
    $this->assertSession()->statusCodeEquals(403);

    // Test authenticated user with permission.
    $this->drupalLogin($this->submitterUser);
    $this->drupalGet('/application');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Document Registration');
  }

  /**
   * Tests the admin submissions list.
   */
  public function testAdminSubmissionsList() {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/document-submissions');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Document Submissions');
  }

  /**
   * Tests user status page.
   */
  public function testUserStatusPage() {
    $this->drupalLogin($this->submitterUser);
    $this->drupalGet('/application-status');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('No document submissions found');
  }

  /**
   * Tests permissions.
   */
  public function testPermissions() {
    // Test that regular user cannot access admin pages.
    $regular_user = $this->drupalCreateUser();
    $this->drupalLogin($regular_user);
    
    $this->drupalGet('/admin/config/document-approval/settings');
    $this->assertSession()->statusCodeEquals(403);
    
    $this->drupalGet('/admin/document-submissions');
    $this->assertSession()->statusCodeEquals(403);
    
    $this->drupalGet('/application');
    $this->assertSession()->statusCodeEquals(403);
  }
}
