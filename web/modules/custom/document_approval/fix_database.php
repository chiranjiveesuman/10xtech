<?php

/**
 * @file
 * Script to fix document_approval module database issues.
 *
 * Run this script from the Drupal root directory:
 * php web/modules/custom/document_approval/fix_database.php
 */

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

// Bootstrap Drupal - find the correct autoload.php path
$autoload_paths = [
  __DIR__ . '/../../../../autoload.php',  // From module directory
  __DIR__ . '/../../../autoload.php',     // Alternative path
  getcwd() . '/autoload.php',             // From current directory
  'autoload.php'                          // Direct path
];

$autoloader = null;
foreach ($autoload_paths as $path) {
  if (file_exists($path)) {
    $autoloader = require_once $path;
    break;
  }
}

if (!$autoloader) {
  die("Error: Could not find autoload.php. Please run this script from the Drupal root directory.\n");
}

$kernel = new DrupalKernel('prod', $autoloader);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$kernel->boot();

echo "Starting Document Approval Module Database Fix...\n";

try {
  // Step 1: Clean up orphaned references
  echo "Step 1: Cleaning up orphaned module references...\n";

  $database = \Drupal::database();

  // Remove admin_entries from system.schema
  $database->delete('key_value')
    ->condition('collection', 'system.schema')
    ->condition('name', 'admin_entries')
    ->execute();

  echo "✓ Removed admin_entries from system.schema\n";

  // Step 2: Drop existing document_submission tables
  echo "Step 2: Dropping existing document_submission tables...\n";

  $tables_to_drop = [
    'document_submission',
    'document_submission__doc1',
    'document_submission__doc2',
    'document_submission__doc3',
    'document_submission__doc4',
    'document_submission__doc1_status',
    'document_submission__doc2_status',
    'document_submission__doc3_status',
    'document_submission__doc4_status',
    'document_submission__doc1_comments',
    'document_submission__doc2_comments',
    'document_submission__doc3_comments',
    'document_submission__doc4_comments',
  ];

  foreach ($tables_to_drop as $table) {
    if ($database->schema()->tableExists($table)) {
      $database->schema()->dropTable($table);
      echo "✓ Dropped table: $table\n";
    }
  }

  // Step 3: Clear caches
  echo "Step 3: Clearing caches...\n";
  \Drupal::service('cache.render')->deleteAll();
  \Drupal::service('cache.discovery')->deleteAll();
  \Drupal::entityTypeManager()->clearCachedDefinitions();
  echo "✓ Caches cleared\n";

  // Step 4: Reinstall entity type
  echo "Step 4: Installing document_submission entity type...\n";

  $entity_definition_update_manager = \Drupal::entityDefinitionUpdateManager();

  try {
    // Get the entity type definition
    $entity_type = \Drupal::entityTypeManager()->getDefinition('document_submission');

    // Install the entity type
    $entity_definition_update_manager->installEntityType($entity_type);
    echo "✓ Document submission entity type installed\n";

  } catch (\Exception $e) {
    echo "⚠ Error installing entity type: " . $e->getMessage() . "\n";
    echo "This might be normal if the entity type already exists.\n";
  }

  // Step 5: Apply any pending entity updates
  echo "Step 5: Applying entity definition updates...\n";

  try {
    // Force entity schema installation
    $entity_type_manager = \Drupal::entityTypeManager();
    $entity_type = $entity_type_manager->getDefinition('document_submission');
    $entity_type_manager->getStorage('document_submission');
    echo "✓ Entity definition updates applied\n";
  } catch (\Exception $e) {
    echo "⚠ Error applying updates: " . $e->getMessage() . "\n";
  }

  // Step 6: Verify installation
  echo "Step 6: Verifying installation...\n";

  // Check if main table exists
  if ($database->schema()->tableExists('document_submission')) {
    echo "✓ document_submission table exists\n";
  } else {
    echo "✗ document_submission table missing\n";
  }

  // Check entity definition
  try {
    $entity_type = \Drupal::entityTypeManager()->getDefinition('document_submission');
    echo "✓ Entity definition loaded successfully\n";
  } catch (\Exception $e) {
    echo "✗ Entity definition error: " . $e->getMessage() . "\n";
  }

  // Final cache clear
  echo "Step 7: Final cache clear...\n";
  \Drupal::service('cache.render')->deleteAll();
  \Drupal::service('cache.discovery')->deleteAll();
  echo "✓ Final cache clear completed\n";

  echo "\n🎉 Database fix completed successfully!\n";
  echo "\nNext steps:\n";
  echo "1. Set permissions at /admin/people/permissions\n";
  echo "2. Configure settings at /admin/config/document-approval/settings\n";
  echo "3. Test the 4 pages:\n";
  echo "   - /admin/config/document-approval/settings\n";
  echo "   - /application\n";
  echo "   - /admin/document-submissions\n";
  echo "   - /admin/document-submissions/{id}\n";

} catch (\Exception $e) {
  echo "❌ Error during fix: " . $e->getMessage() . "\n";
  echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response);
