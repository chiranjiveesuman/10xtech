<?php

namespace Drupal\group_display\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines an interface for Entity View Builder plugins.
 */
interface EntityViewBuilderInterface extends PluginInspectionInterface {

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
  public function buildFull(array $build, EntityInterface $entity);

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
  public function buildTeaser(array $build, EntityInterface $entity);

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
  public function buildCard(array $build, EntityInterface $entity);

}
