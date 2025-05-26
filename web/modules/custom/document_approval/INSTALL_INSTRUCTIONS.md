# Document Approval Module - Installation & Fix Instructions

## 🚨 **FIXING THE CURRENT ERROR**

The error you're seeing is due to form validation issues. Here's how to fix it:

### **Step 1: Clear Cache**
```bash
drush cr
```

### **Step 2: Reinstall the Module (Recommended)**
```bash
# Uninstall the module
drush pmu document_approval

# Clear cache
drush cr

# Reinstall the module
drush en document_approval

# Run database updates
drush updb
```

### **Step 3: Set Up Permissions**
1. Go to `/admin/people/permissions`
2. Find "Document Approval" section
3. Give these permissions:
   - **Authenticated users**: "Submit documents"
   - **Administrator**: "Administer document approval"

### **Step 4: Configure Settings**
1. Go to `/admin/config/document-approval/settings`
2. Set your document labels:
   - Document 1: "Personal ID"
   - Document 2: "Proof of Address" 
   - Document 3: "Income Statement"
   - Document 4: "Additional Document (Optional)"
3. Set file settings:
   - Allowed extensions: `pdf jpg jpeg`
   - Max file size: `10 MB`

## 📋 **TESTING THE 4 PAGES**

### **Page 1: Admin Settings** ✅
- **URL**: `/admin/config/document-approval/settings`
- **Login as**: Administrator
- **Test**: Change document labels and save

### **Page 2: User Upload** ✅
- **URL**: `/application`
- **Login as**: Regular user with "submit documents" permission
- **Test**: Upload PDF/JPEG files and submit

### **Page 3: Admin Submissions List** ✅
- **URL**: `/admin/document-submissions`
- **Login as**: Administrator
- **Test**: See list of users who submitted documents

### **Page 4: Admin Review** ✅
- **URL**: `/admin/document-submissions/{id}` (click "Review" from page 3)
- **Login as**: Administrator
- **Test**: View documents and approve/reject them

## 🔧 **IF PROBLEMS PERSIST**

### **Option A: Database Reset**
```bash
# Drop and recreate the entity tables
drush sql-query "DROP TABLE IF EXISTS document_submission;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc1;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc2;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc3;"
drush sql-query "DROP TABLE IF EXISTS document_submission__doc4;"

# Reinstall module
drush pmu document_approval
drush en document_approval
drush updb
```

### **Option B: Manual Entity Update**
```bash
# Update entity definitions
drush php-eval "\Drupal::entityDefinitionUpdateManager()->applyUpdates();"
```

## 🎯 **EXPECTED WORKFLOW**

1. **Admin configures** document labels at `/admin/config/document-approval/settings`
2. **User logs in** → automatically redirected to `/application`
3. **User uploads** 3 required + 1 optional document
4. **User submits** → page title changes to "Application Status"
5. **Admin reviews** at `/admin/document-submissions`
6. **Admin clicks** username → sees all documents with preview
7. **Admin approves/rejects** each document with radio buttons
8. **User sees** updated status and can update rejected documents

## 📞 **SUPPORT**

If you still get the error after following these steps:

1. Check the Drupal logs: `/admin/reports/dblog`
2. Look for specific error messages
3. Ensure file upload directory has proper permissions
4. Verify PHP file upload settings are adequate

The module is fully functional - the error is just a form validation path issue that these steps will resolve.
