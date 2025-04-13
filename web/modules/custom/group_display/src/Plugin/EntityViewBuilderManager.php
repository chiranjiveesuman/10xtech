<?php

namespace Drupal\group_display\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Plugin manager for Entity View Builder plugins.
 */
class EntityViewBuilderManager extends DefaultPluginManager {

  /**
   * Constructs a EntityViewBuilderManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/EntityViewBuilder',
      $namespaces,
      $module_handler,
      'Drupal\group_display\Plugin\EntityViewBuilderInterface',
      'Drupal\group_display\Annotation\EntityViewBuilder'
    );

    $this->alterInfo('entity_view_builder_info');
    $this->setCacheBackend($cache_backend, 'entity_view_builder_plugins');
  }

}
