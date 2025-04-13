<?php

namespace Drupal\group_display\Plugin\EntityViewBuilder;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\group_display\EntityViewBuilder\NodeViewBuilderAbstract;
use Drupal\node\NodeInterface;
use Drupal\og\Og;
use Drupal\og\OgMembershipInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The "Node Group" plugin.
 *
 * @EntityViewBuilder(
 *   id = "node.group",
 *   label = @Translation("Node - Group"),
 *   description = "Node view builder for Group bundle."
 * )
 */
class NodeGroup extends NodeViewBuilderAbstract {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->currentUser = $container->get('current_user');
    return $instance;
  }

  /**
   * Build full view mode.
   *
   * @param array $build
   *   The existing build.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return array
   *   Render array.
   */
  public function buildFull(array $build, EntityInterface $entity) {
    if (!$entity instanceof NodeInterface) {
      return $build;
    }

    // Header.
    $build[] = $this->buildHeroHeader($entity, 'field_featured_image');

    // Add subscription message for authenticated users who are not members.
    if ($this->currentUser->isAuthenticated() && $this->canUserSubscribe($entity)) {
      $build[] = $this->buildSubscriptionMessage($entity);
    }

    // Body.
    $element = $this->buildProcessedText($entity);
    $build[] = $this->wrapElementWideContainer($element);

    return $build;
  }

  /**
   * Checks if the current user can subscribe to the group.
   *
   * @param \Drupal\node\NodeInterface $entity
   *   The group entity.
   *
   * @return bool
   *   TRUE if the user can subscribe, FALSE otherwise.
   */
  protected function canUserSubscribe(NodeInterface $entity) {
    // Check if the user is already a member of the group.
    $membership = Og::getMembership($entity, $this->currentUser);
    if ($membership) {
      return FALSE;
    }

    // Check if the user is allowed to subscribe to the group.
    return Og::isGroup('node', $entity->bundle()) && 
           Og::getMembershipEntityTypeId() && 
           Og::isMembershipOpen($entity) && 
           Og::userAccess($entity, 'subscribe', $this->currentUser)->isAllowed();
  }

  /**
   * Builds a subscription message for the user.
   *
   * @param \Drupal\node\NodeInterface $entity
   *   The group entity.
   *
   * @return array
   *   Render array.
   */
  protected function buildSubscriptionMessage(NodeInterface $entity) {
    $url = Url::fromRoute('og.subscribe', [
      'entity_type_id' => 'node',
      'group' => $entity->id(),
    ]);

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['group-subscription-message'],
      ],
      'message' => [
        '#markup' => $this->t('Hi @name, click <a href="@url">here</a> if you would like to subscribe to this group called @label', [
          '@name' => $this->currentUser->getDisplayName(),
          '@url' => $url->toString(),
          '@label' => $entity->label(),
        ]),
      ],
      '#weight' => -99,
    ];
  }

  /**
   * Build teaser view mode.
   *
   * @param array $build
   *   The existing build.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return array
   *   Render array.
   */
  public function buildTeaser(array $build, EntityInterface $entity) {
    $image_info = $this->getMediaImageAndAlt($entity, 'field_featured_image');

    $element = parent::buildTeaser($build, $entity);
    $element += [
      '#image' => $image_info['url'] ?? NULL,
      '#image_alt' => $image_info['alt'] ?? NULL,
      '#body' => $this->buildProcessedText($entity),
    ];

    $build[] = $element;

    return $build;
  }

}
