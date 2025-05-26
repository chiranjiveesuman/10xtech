<?php

/**
 * Simple database fix for Document Approval module
 * This bypasses the entity system and creates the table directly
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

echo "🔧 Simple Document Approval Database Fix\n";
echo "========================================\n\n";

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
  
  // Step 2: Create the table manually
  echo "\nStep 2: Creating document_submission table...\n";
  
  $schema = [
    'description' => 'Stores document submission data.',
    'fields' => [
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
    ],
    'primary key' => ['id'],
    'unique keys' => [
      'uuid' => ['uuid'],
    ],
    'indexes' => [
      'user_id' => ['user_id'],
      'status' => ['status'],
    ],
  ];
  
  $database->schema()->createTable('document_submission', $schema);
  echo "✓ document_submission table created successfully\n";
  
  // Step 3: Verify table creation
  echo "\nStep 3: Verifying table creation...\n";
  if ($database->schema()->tableExists('document_submission')) {
    echo "✓ Table exists and is ready\n";
    
    // Show table structure
    $result = $database->query("DESCRIBE document_submission")->fetchAll();
    echo "✓ Table structure:\n";
    foreach ($result as $row) {
      echo "  - {$row->Field} ({$row->Type})\n";
    }
  } else {
    echo "✗ Table creation failed\n";
  }
  
  // Step 4: Clear caches
  echo "\nStep 4: Clearing caches...\n";
  \Drupal::service('cache.render')->deleteAll();
  \Drupal::service('cache.discovery')->deleteAll();
  echo "✓ Caches cleared\n";
  
  echo "\n🎉 Simple fix completed successfully!\n";
  echo "\nThe document_submission table is now ready.\n";
  echo "You can now test the application at /application\n";
  
} catch (\Exception $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
  echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response);
