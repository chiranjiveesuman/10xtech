# Document Approval System

A Drupal 10.3 custom module that provides a comprehensive document upload and approval system.

## Features

### For Users
- **Document Registration**: Users can upload up to 4 documents (3 required, 1 optional)
- **Supported File Types**: PDF and JPEG files
- **Application Status**: Real-time status tracking of submitted documents
- **Document Updates**: Users can update rejected documents
- **Auto-redirect**: Users are automatically redirected to the application page after login

### For Administrators
- **Admin Settings**: Configure document labels and file upload settings
- **Document Review**: View and approve/reject submitted documents
- **Document Preview**: View PDF and image files directly in the browser
- **User Management**: See list of all users who have submitted documents
- **Comments System**: Add comments when rejecting documents

## Installation

1. Place the module in `web/modules/custom/document_approval/`
2. Enable the module: `drush en document_approval`
3. Configure permissions at `/admin/people/permissions`
4. Configure settings at `/admin/config/document-approval/settings`

## Permissions

- **Submit documents**: Allows users to submit documents for approval
- **Administer document approval**: Allows administrators to review submissions and configure settings

## Usage

### User Workflow
1. User logs in and is redirected to `/application`
2. User sees "Document Registration" page with upload forms
3. User uploads required documents and submits
4. Page title changes to "Application Status"
5. User can view status and update rejected documents

### Admin Workflow
1. Admin accesses `/admin/document-submissions` to see all submissions
2. Admin clicks on a user to review their documents
3. Admin can view documents in browser (not download)
4. Admin approves or rejects each document with optional comments
5. Overall status is automatically calculated based on individual document statuses

## Configuration

### Document Labels
Administrators can customize the labels for each document type:
- Document 1: Personal Identification (required)
- Document 2: Proof of Address (required)
- Document 3: Income Statement (required)
- Document 4: Additional Document (optional)

### File Settings
- **Allowed Extensions**: pdf jpg jpeg (configurable)
- **Maximum File Size**: 10 MB (configurable)

## Database Schema

The module creates a `document_submission` entity with the following fields:
- User ID (reference to user)
- Overall status (pending/approved/rejected)
- 4 file fields (doc1-doc4)
- 4 status fields (doc1_status-doc4_status)
- 4 comment fields (doc1_comments-doc4_comments)
- Created and changed timestamps

## Status Logic

- **Pending**: Initial status when documents are submitted
- **Approved**: All required documents are approved
- **Rejected**: Any document is rejected

## Routes

- `/application` - User document submission/status page
- `/application-status` - User status page
- `/admin/config/document-approval/settings` - Admin settings
- `/admin/document-submissions` - Admin submissions list
- `/admin/document-submissions/{id}` - Admin review page

## Styling

The module includes responsive CSS styling for:
- Document upload forms
- Status indicators
- Admin preview interface
- Mobile-friendly design

## Testing

Run the functional tests:
```bash
vendor/bin/phpunit web/modules/custom/document_approval/tests/
```

## Requirements

- Drupal 10.3+
- File module
- User module

## Support

This module provides a complete document approval workflow suitable for applications requiring document verification and approval processes.
