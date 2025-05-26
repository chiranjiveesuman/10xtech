@echo off
echo Document Approval Module Fix Script
echo ====================================

echo.
echo Step 1: Cleaning up database issues...
.\vendor\bin\drush sql-query "DELETE FROM key_value WHERE collection = 'system.schema' AND name = 'admin_entries';"

echo.
echo Step 2: Dropping existing tables...
.\vendor\bin\drush sql-query "DROP TABLE IF EXISTS document_submission;"
.\vendor\bin\drush sql-query "DROP TABLE IF EXISTS document_submission__doc1;"
.\vendor\bin\drush sql-query "DROP TABLE IF EXISTS document_submission__doc2;"
.\vendor\bin\drush sql-query "DROP TABLE IF EXISTS document_submission__doc3;"
.\vendor\bin\drush sql-query "DROP TABLE IF EXISTS document_submission__doc4;"

echo.
echo Step 3: Force uninstall module...
.\vendor\bin\drush pmu document_approval -y

echo.
echo Step 4: Clear caches...
.\vendor\bin\drush cr

echo.
echo Step 5: Clear entity definitions...
.\vendor\bin\drush php-eval "\Drupal::entityTypeManager()->clearCachedDefinitions();"

echo.
echo Step 6: Reinstall module...
.\vendor\bin\drush en document_approval -y

echo.
echo Step 7: Run database updates...
.\vendor\bin\drush updb -y

echo.
echo Step 8: Apply entity updates...
.\vendor\bin\drush php-eval "\Drupal::entityDefinitionUpdateManager()->applyUpdates();"

echo.
echo Step 9: Final cache clear...
.\vendor\bin\drush cr

echo.
echo Step 10: Verify installation...
.\vendor\bin\drush sql-query "SHOW TABLES LIKE 'document_submission';"

echo.
echo ====================================
echo Fix completed!
echo.
echo Next steps:
echo 1. Go to /admin/people/permissions and set permissions
echo 2. Go to /admin/config/document-approval/settings to configure
echo 3. Test the 4 pages:
echo    - /admin/config/document-approval/settings
echo    - /application  
echo    - /admin/document-submissions
echo    - /admin/document-submissions/{id}
echo.
pause
