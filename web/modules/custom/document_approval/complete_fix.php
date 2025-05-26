<?php

/**
 * Complete database fix for Document Approval module
 * Creates the full table with all document fields
 */

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

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

$kernel = new DrupalKernel('prod', $autoloader);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$kernel->boot();

echo "🔧 Complete Document Approval Database Fix\n";
echo "==========================================\n\n";

try {
  $database = \Drupal::database();
  
  // Step 1: Drop existing table if it exists
  echo "Step 1: Cleaning up existing table...\n";
  if ($database->schema()->tableExists('document_submission')) {
    $database->schema()->dropTable('document_submission');
    echo "✓ Dropped existing document_submission table\n";
  } else {
    echo "✓ No existing table to clean up\n";
  }
  
  // Step 2: Create the complete table with all document fields
  echo "\nStep 2: Creating complete document_submission table...\n";
  
  $schema = [
    'description' => 'Stores document submission data with all document fields.',
    'fields' => [
      // Base entity fields
      'id' => [
        'type' => 'serial',
        'not null' => TRUE,
        'description' => 'Primary Key: Unique document submission ID.',
      ],
      'uuid' => [
        'type' => 'varchar_ascii',
        'length' => 128,
        'not null' => TRUE,
        'default' => '',
        'description' => 'Unique Key: Universally unique identifier for this entity.',
      ],
      'user_id' => [
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
        'description' => 'The user ID of the submission author.',
      ],
      'status' => [
        'type' => 'varchar',
        'length' => 32,
        'not null' => TRUE,
        'default' => 'pending',
        'description' => 'The submission status.',
      ],
      'created' => [
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
        'description' => 'The time that the submission was created.',
      ],
      'changed' => [
        'type' => 'int',
        'not null' => TRUE,
        'default' => 0,
        'description' => 'The time that the submission was last edited.',
      ],
      
      // Document 1 fields
      'doc1__target_id' => [
        'type' => 'int',
        'unsigned' => TRUE,
        'description' => 'The ID of the target file entity for document 1.',
      ],
      'doc1__display' => [
        'type' => 'int',
        'size' => 'tiny',
        'unsigned' => TRUE,
        'default' => 1,
        'description' => 'Flag to control whether this file should be displayed when viewing content.',
      ],
      'doc1__description' => [
        'type' => 'text',
        'description' => 'A description of the file for document 1.',
      ],
      'doc1_status' => [
        'type' => 'varchar',
        'length' => 32,
        'default' => 'pending',
        'description' => 'Approval status for document 1.',
      ],
      'doc1_comments' => [
        'type' => 'text',
        'description' => 'Admin comments for document 1.',
      ],
      
      // Document 2 fields
      'doc2__target_id' => [
        'type' => 'int',
        'unsigned' => TRUE,
        'description' => 'The ID of the target file entity for document 2.',
      ],
      'doc2__display' => [
        'type' => 'int',
        'size' => 'tiny',
        'unsigned' => TRUE,
        'default' => 1,
        'description' => 'Flag to control whether this file should be displayed when viewing content.',
      ],
      'doc2__description' => [
        'type' => 'text',
        'description' => 'A description of the file for document 2.',
      ],
      'doc2_status' => [
        'type' => 'varchar',
        'length' => 32,
        'default' => 'pending',
        'description' => 'Approval status for document 2.',
      ],
      'doc2_comments' => [
        'type' => 'text',
        'description' => 'Admin comments for document 2.',
      ],
      
      // Document 3 fields
      'doc3__target_id' => [
        'type' => 'int',
        'unsigned' => TRUE,
        'description' => 'The ID of the target file entity for document 3.',
      ],
      'doc3__display' => [
        'type' => 'int',
        'size' => 'tiny',
        'unsigned' => TRUE,
        'default' => 1,
        'description' => 'Flag to control whether this file should be displayed when viewing content.',
      ],
      'doc3__description' => [
        'type' => 'text',
        'description' => 'A description of the file for document 3.',
      ],
      'doc3_status' => [
        'type' => 'varchar',
        'length' => 32,
        'default' => 'pending',
        'description' => 'Approval status for document 3.',
      ],
      'doc3_comments' => [
        'type' => 'text',
        'description' => 'Admin comments for document 3.',
      ],
      
      // Document 4 fields
      'doc4__target_id' => [
        'type' => 'int',
        'unsigned' => TRUE,
        'description' => 'The ID of the target file entity for document 4.',
      ],
      'doc4__display' => [
        'type' => 'int',
        'size' => 'tiny',
        'unsigned' => TRUE,
        'default' => 1,
        'description' => 'Flag to control whether this file should be displayed when viewing content.',
      ],
      'doc4__description' => [
        'type' => 'text',
        'description' => 'A description of the file for document 4.',
      ],
      'doc4_status' => [
        'type' => 'varchar',
        'length' => 32,
        'default' => 'pending',
        'description' => 'Approval status for document 4.',
      ],
      'doc4_comments' => [
        'type' => 'text',
        'description' => 'Admin comments for document 4.',
      ],
    ],
    'primary key' => ['id'],
    'unique keys' => [
      'uuid' => ['uuid'],
    ],
    'indexes' => [
      'user_id' => ['user_id'],
      'status' => ['status'],
      'doc1_target_id' => ['doc1__target_id'],
      'doc2_target_id' => ['doc2__target_id'],
      'doc3_target_id' => ['doc3__target_id'],
      'doc4_target_id' => ['doc4__target_id'],
    ],
  ];
  
  $database->schema()->createTable('document_submission', $schema);
  echo "✓ Complete document_submission table created successfully\n";
  
  // Step 3: Verify table creation
  echo "\nStep 3: Verifying table creation...\n";
  if ($database->schema()->tableExists('document_submission')) {
    echo "✓ Table exists and is ready\n";
    
    // Count columns
    $result = $database->query("DESCRIBE document_submission")->fetchAll();
    echo "✓ Table has " . count($result) . " columns (should be 26)\n";
    
    // Check for key columns
    $columns = array_column($result, 'Field');
    $required_columns = ['doc1__target_id', 'doc2__target_id', 'doc3__target_id', 'doc4__target_id'];
    foreach ($required_columns as $col) {
      if (in_array($col, $columns)) {
        echo "✓ Column $col exists\n";
      } else {
        echo "✗ Column $col missing\n";
      }
    }
  } else {
    echo "✗ Table creation failed\n";
  }
  
  // Step 4: Clear caches
  echo "\nStep 4: Clearing caches...\n";
  \Drupal::service('cache.render')->deleteAll();
  \Drupal::service('cache.discovery')->deleteAll();
  echo "✓ Caches cleared\n";
  
  echo "\n🎉 Complete fix completed successfully!\n";
  echo "\nThe document_submission table now has all required fields:\n";
  echo "- Base fields: id, uuid, user_id, status, created, changed\n";
  echo "- Document fields: doc1-4 with target_id, display, description\n";
  echo "- Status fields: doc1-4 with status and comments\n";
  echo "\nYou can now test:\n";
  echo "1. Document upload at /application\n";
  echo "2. Admin submissions list at /admin/document-submissions\n";
  
} catch (\Exception $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
  echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response);
