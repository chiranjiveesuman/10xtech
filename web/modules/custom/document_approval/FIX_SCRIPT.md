# Document Approval Module - Complete Fix Script

## 🚨 **CRITICAL FIXES NEEDED**

Based on the Issues.md file, here are the exact steps to fix all problems:

## **Step 1: Clean Up Database Issues**

### **Remove the problematic admin_entries reference:**
```bash
# Remove the orphaned module reference
drush sql-query "DELETE FROM key_value WHERE collection = 'system.schema' AND name = 'admin_entries';"

# Remove from core.extension config if it exists
drush sql-query "DELETE FROM config WHERE name = 'core.extension' AND data LIKE '%admin_entries%';"

# Clear cache
drush cr
```

### **Fix the missing document_submission table:**
```bash
# Drop any existing broken tables
drush sql-query "DROP TABLE IF EXISTS document_submission;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc1;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc2;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc3;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc4;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc1_status;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc2_status;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc3_status;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc4_status;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc1_comments;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc2_comments;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc3_comments;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc4_comments;"
```

## **Step 2: Complete Module Reinstallation**

```bash
# Force uninstall the module (ignore errors)
drush pmu document_approval -y

# Clear all caches
drush cr

# Clear entity cache specifically
drush php-eval "\Drupal::entityTypeManager()->clearCachedDefinitions();"

# Reinstall the module
drush en document_approval -y

# Run database updates
drush updb -y

# Apply entity updates
drush php-eval "\Drupal::entityDefinitionUpdateManager()->applyUpdates();"

# Final cache clear
drush cr
```

## **Step 3: Verify Installation**

```bash
# Check if the table exists
drush sql-query "SHOW TABLES LIKE 'document_submission';"

# Check entity definition
drush php-eval "var_dump(\Drupal::entityTypeManager()->getDefinition('document_submission'));"
```

## **Step 4: Set Up Permissions**

1. Go to `/admin/people/permissions`
2. Find "Document Approval" section
3. Assign permissions:
   - **Authenticated users**: "Submit documents"
   - **Administrator**: "Administer document approval"

## **Step 5: Configure Settings**

1. Go to `/admin/config/document-approval/settings`
2. Set document labels and file settings
3. Save configuration

## **Step 6: Test the 4 Pages**

### **Test Page 1: Admin Settings**
- URL: `/admin/config/document-approval/settings`
- Should show form to configure document labels

### **Test Page 2: User Upload**
- URL: `/application`
- Should show document upload form

### **Test Page 3: Admin Submissions**
- URL: `/admin/document-submissions`
- Should show list of submissions (empty initially)

### **Test Page 4: Admin Review**
- URL: `/admin/document-submissions/{id}`
- Will be available after users submit documents

## **Alternative: Nuclear Option**

If the above doesn't work, use this complete reset:

```bash
# Remove all traces of the module
drush sql-query "DELETE FROM config WHERE name LIKE 'document_approval%';"
drush sql-query "DELETE FROM key_value WHERE name LIKE 'document_approval%';"
drush sql-query "DELETE FROM key_value WHERE name = 'admin_entries';"

# Remove from core.extension
drush config-delete core.extension

# Reinstall core.extension
drush config-import --partial --source=web/core/config/install

# Clear everything
drush cr
drush php-eval "\Drupal::entityTypeManager()->clearCachedDefinitions();"

# Reinstall module
drush en document_approval -y
drush updb -y
drush cr
```

## **Expected Results**

After following these steps:
- ✅ No more "Table 'new.document_submission' doesn't exist" errors
- ✅ No more "admin_entries does not exist" errors
- ✅ All 4 pages working correctly
- ✅ File upload functionality working
- ✅ Admin approval workflow functional

## **If Problems Persist**

Check the Drupal logs:
```bash
drush watchdog:show --type=document_approval
```

The module code is correct - these are just database installation issues that these steps will resolve.
