<?php

namespace Drupal\Tests\group_display\Functional;

use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\og\Entity\OgMembership;
use Drupal\og\Og;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the group subscription functionality.
 *
 * @group group_display
 */
class GroupSubscriptionTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'og',
    'og_ui',
    'group_display',
  ];

  /**
   * A test group.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $group;

  /**
   * A test user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create a 'group' content type.
    $group_type = NodeType::create([
      'type' => 'group',
      'name' => 'Group',
    ]);
    $group_type->save();

    // Add OG group field to the group content type.
    Og::groupTypeManager()->addGroup('node', 'group');

    // Create a group node.
    $this->group = Node::create([
      'type' => 'group',
      'title' => 'Test Group',
    ]);
    $this->group->save();

    // Create a test user.
    $this->user = $this->drupalCreateUser(['access content']);
  }

  /**
   * Tests that the subscription message is displayed to authenticated users.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testGroupSubscription() {
    // Check that we can access the group page as anonymous user.
    $this->drupalGet($this->group->toUrl());

    // Verify that the subscription message is not shown to anonymous users.
    $this->assertSession()->responseNotContains('click here if you would like to subscribe to this group');

    // Login as the test user.
    $this->drupalLogin($this->user);

    // Visit the group node.
    $this->drupalGet($this->group->toUrl());

    // Check that the subscription message is displayed.
    $this->assertSession()->pageTextContains('Hi ' . $this->user->getAccountName());
    $this->assertSession()->pageTextContains('click here if you would like to subscribe to this group called Test Group');

    // Check that the subscription link is present.
    $this->assertSession()->linkExists('here');

    // Create a second user who will be a member of the group.
    $member = $this->drupalCreateUser(['access content']);

    // Create and save a membership.
    $membership = OgMembership::create();
    $membership->setOwner($member)
      ->setGroup($this->group)
      ->setState(OgMembership::STATE_ACTIVE)
      ->save();

    // Login as the member user.
    $this->drupalLogin($member);

    // Visit the group node.
    $this->drupalGet($this->group->toUrl());

    // Verify that the subscription message is not shown to members.
    $this->assertSession()->responseNotContains('click here if you would like to subscribe to this group');
  }

}