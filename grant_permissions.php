<?php

/**
 * @file
 * Script to grant submit documents permission to authenticated users.
 */

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

// Bootstrap Drupal
$autoloader = require_once 'vendor/autoload.php';
$kernel = new DrupalKernel('prod', $autoloader);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$kernel->boot();

// Grant permission to authenticated users
if (\Drupal::moduleHandler()->moduleExists('user')) {
  user_role_grant_permissions('authenticated', ['submit documents']);
  echo "Granted 'submit documents' permission to authenticated users.\n";
} else {
  echo "User module not found.\n";
}

// Clear cache
drupal_flush_all_caches();
echo "Cache cleared.\n";

echo "Done!\n";
