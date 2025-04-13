<?php

namespace Drupal\group_display\EntityViewBuilder;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Abstract class for node view builders.
 */
abstract class NodeViewBuilderAbstract {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new NodeViewBuilderAbstract object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    RendererInterface $renderer,
    ModuleHandlerInterface $module_handler,
    MessengerInterface $messenger
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
    $this->moduleHandler = $module_handler;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('module_handler'),
      $container->get('messenger')
    );
  }

  /**
   * Get the messenger service.
   *
   * @return \Drupal\Core\Messenger\MessengerInterface
   *   The messenger service.
   */
  protected function messenger() {
    return $this->messenger;
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
  abstract public function buildFull(array $build, EntityInterface $entity);

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
    return [
      '#theme' => 'server_theme_teaser',
      '#title' => $entity->label(),
      '#url' => $entity->toUrl(),
    ];
  }

  /**
   * Build card view mode.
   *
   * @param array $build
   *   The existing build.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return array
   *   Render array.
   */
  public function buildCard(array $build, EntityInterface $entity) {
    return $this->buildTeaser($build, $entity);
  }

  /**
   * Get a referenced entity from a field.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $field_name
   *   The field name.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The referenced entity, or NULL if not found.
   */
  protected function getReferencedEntityFromField(EntityInterface $entity, $field_name) {
    if (!$entity->hasField($field_name) || $entity->get($field_name)->isEmpty()) {
      return NULL;
    }

    return $entity->get($field_name)->entity;
  }

  /**
   * Build a processed text field.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $field_name
   *   The field name. Defaults to "body".
   *
   * @return array
   *   Render array.
   */
  protected function buildProcessedText(EntityInterface $entity, $field_name = 'body') {
    if (!$entity->hasField($field_name) || $entity->get($field_name)->isEmpty()) {
      return [];
    }

    return [
      '#type' => 'processed_text',
      '#text' => $entity->get($field_name)->value,
      '#format' => $entity->get($field_name)->format,
    ];
  }

  /**
   * Build a hero header with an image.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $field_name
   *   The field name of the image.
   *
   * @return array
   *   Render array.
   */
  protected function buildHeroHeader(EntityInterface $entity, $field_name) {
    $media = $this->getReferencedEntityFromField($entity, $field_name);
    $image = $media instanceof MediaInterface ? $this->buildImageStyle($media, 'hero', 'field_media_image') : NULL;

    return [
      '#theme' => 'server_theme_hero_header',
      '#title' => $entity->label(),
      '#image' => $image,
    ];
  }

  /**
   * Build an image style.
   *
   * @param \Drupal\media\MediaInterface $media
   *   The media entity.
   * @param string $image_style
   *   The image style.
   * @param string $field_name
   *   The field name of the image. Defaults to "field_media_image".
   *
   * @return array|null
   *   Render array or NULL if no image found.
   */
  protected function buildImageStyle(MediaInterface $media, $image_style, $field_name = 'field_media_image') {
    if (!$media->hasField($field_name) || $media->get($field_name)->isEmpty()) {
      return NULL;
    }

    $image = $media->get($field_name);
    $build = [
      '#theme' => 'image_style',
      '#style_name' => $image_style,
      '#uri' => $image->entity->getFileUri(),
      '#alt' => $image->alt,
    ];

    return $build;
  }

  /**
   * Build content tags.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $field_name
   *   The field name. Defaults to "field_tags".
   *
   * @return array
   *   Render array.
   */
  protected function buildContentTags(EntityInterface $entity, $field_name = 'field_tags') {
    if (!$entity->hasField($field_name) || $entity->get($field_name)->isEmpty()) {
      return [];
    }

    $items = [];
    foreach ($entity->get($field_name)->referencedEntities() as $term) {
      $items[] = [
        '#theme' => 'server_theme_content_tag',
        '#title' => $term->label(),
        '#url' => Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $term->id()]),
      ];
    }

    return [
      '#theme' => 'server_theme_content_tags',
      '#items' => $items,
    ];
  }

  /**
   * Build tags.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $field_name
   *   The field name. Defaults to "field_tags".
   *
   * @return array
   *   Array of tag labels.
   */
  protected function buildTags(EntityInterface $entity, $field_name = 'field_tags') {
    if (!$entity->hasField($field_name) || $entity->get($field_name)->isEmpty()) {
      return [];
    }

    $tags = [];
    foreach ($entity->get($field_name)->referencedEntities() as $term) {
      $tags[] = $term->label();
    }

    return $tags;
  }

  /**
   * Wrap an element with a wide container.
   *
   * @param array $element
   *   The element to wrap.
   *
   * @return array
   *   Render array.
   */
  protected function wrapElementWideContainer(array $element) {
    return [
      '#theme' => 'server_theme_container',
      '#element' => $element,
      '#is_wide' => TRUE,
    ];
  }

  /**
   * Get the media image URL and alt text.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $field_name
   *   The field name.
   * @param string $image_style
   *   The image style. Defaults to "thumbnail".
   *
   * @return array
   *   Array with keys:
   *   - url: The URL of the image.
   *   - alt: The alt text.
   */
  protected function getMediaImageAndAlt(EntityInterface $entity, $field_name, $image_style = 'thumbnail') {
    $info = [
      'url' => NULL,
      'alt' => NULL,
    ];

    $media = $this->getReferencedEntityFromField($entity, $field_name);
    if (!$media instanceof MediaInterface) {
      return $info;
    }

    if (!$media->hasField('field_media_image') || $media->get('field_media_image')->isEmpty()) {
      return $info;
    }

    $image = $media->get('field_media_image');
    $file_uri = $image->entity->getFileUri();

    $style = $this->entityTypeManager->getStorage('image_style')->load($image_style);
    if (!$style) {
      return $info;
    }

    $info = [
      'url' => $style->buildUrl($file_uri),
      'alt' => $image->alt,
    ];

    return $info;
  }

}
