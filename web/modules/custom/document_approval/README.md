# Document Approval Module

A comprehensive Drupal 10 module that implements a complete document submission and approval workflow. Built for scenarios where users need to submit documents for administrative review and approval.

## Overview

This module creates a streamlined process where authenticated users can upload documents through a managed interface, and administrators can review, approve, or reject these submissions with detailed feedback. The system handles up to 4 different document types per submission and provides real-time status tracking.

## Complete Workflow

### User Journey
1. **Initial Access**: User logs in and navigates to `/application`
2. **Document Upload**: User uploads required documents using managed file fields
3. **Submission**: System creates a `document_submission` entity with pending status
4. **Status Monitoring**: User can check progress at `/application-status`
5. **Resubmission**: If documents are rejected, user can upload new versions
6. **Completion**: Once all mandatory documents are approved, application is complete

### Admin Journey
1. **Queue Management**: Admin accesses `/admin/document-submissions` for overview
2. **Individual Review**: Admin clicks specific submission for detailed review
3. **Document Analysis**: System displays documents with preview capabilities
4. **Decision Making**: Admin approves/rejects each document with optional comments
5. **Status Updates**: System automatically updates overall submission status
6. **User Notification**: Users see updated status immediately

## Architecture & File Structure

### Core Entity (`src/Entity/DocumentSubmission.php`)
The heart of the system - a content entity that stores all submission data:
- **User reference**: Links submission to specific user account
- **File fields**: Four managed file fields (doc1-doc4) for document storage
- **Status tracking**: Individual status for each document plus overall status
- **Comment system**: Admin feedback storage for rejected documents
- **Timestamps**: Created/modified tracking for audit purposes

### Service Layer (`src/Service/DocumentApprovalService.php`)
Business logic centralization that handles:
- **Status calculations**: Determines overall submission status based on individual document states
- **File management**: Handles file operations and validation
- **Cache invalidation**: Ensures admin interface reflects real-time changes
- **Data consistency**: Maintains referential integrity across the system

### Form Controllers

#### User Interface (`src/Form/UserDocumentSubmissionForm.php`)
The main user interaction point:
- **Dynamic form building**: Generates form based on admin configuration
- **File upload handling**: Uses managed_file elements for robust file management
- **Status display**: Shows current document states with visual indicators
- **Validation logic**: Ensures file types and sizes meet requirements
- **Submission processing**: Creates or updates document_submission entities

#### Admin Interface (`src/Form/AdminApprovalForm.php`)
Administrative review interface:
- **Document preview**: Renders PDFs and images directly in browser
- **Bulk operations**: Allows approval/rejection of multiple documents
- **Comment system**: Provides feedback mechanism for rejected documents
- **Status management**: Updates individual and overall submission status
- **Cache handling**: Ensures changes are immediately visible

#### Configuration (`src/Form/AdminSettingsForm.php`)
System configuration management:
- **Document labeling**: Customizable names for each document type
- **Requirement settings**: Define which documents are mandatory
- **File constraints**: Configure allowed types and size limits
- **System behavior**: Control various workflow aspects

### Controllers

#### Application Controller (`src/Controller/DocumentApprovalController.php`)
Handles main application logic:
- **Application page**: Serves the main document submission interface
- **Submissions list**: Provides admin overview of all submissions
- **Access control**: Ensures proper permissions for different user roles
- **Form integration**: Embeds appropriate forms based on user context

#### Status Controller (`src/Controller/UserStatusController.php`)
User status interface:
- **Status display**: Shows current state of user's submissions
- **Progress tracking**: Visual indicators of approval progress
- **Action links**: Provides navigation to submission forms when needed
- **Congratulations handling**: Special display for completed applications

### Configuration Files

#### Module Definition (`document_approval.info.yml`)
Standard Drupal module metadata:
- **Dependencies**: Declares required core modules (file, user)
- **Version compatibility**: Specifies Drupal core requirements
- **Module information**: Name, description, package classification

#### Routing (`document_approval.routing.yml`)
URL structure and access control:
- **User routes**: `/application` and `/application-status` for end users
- **Admin routes**: Administrative interfaces under `/admin/`
- **Access callbacks**: Permission-based and custom access control
- **Cache settings**: Appropriate caching strategies for each route

#### Permissions (`document_approval.permissions.yml`)
Security model definition:
- **submit documents**: Basic user permission for document submission
- **administer document approval**: Full administrative access

#### Menu Integration (`document_approval.links.menu.yml`)
Navigation structure:
- **Admin configuration**: Settings page in admin config menu
- **Content management**: Submissions list in admin content menu
- **Logical grouping**: Appropriate placement in Drupal's admin structure

#### Local Menu (`document_approval.links.menu.local.yml`)
Account-level navigation:
- **User shortcuts**: Quick access to submission and status pages
- **Account integration**: Links appear in user account context
- **Weight management**: Proper ordering of menu items

### Database Schema (`document_approval.install`)
Installation and update procedures:
- **Entity schema**: Defines document_submission table structure
- **Field definitions**: Creates file, status, and comment fields
- **Permission setup**: Grants default permissions during installation
- **Update hooks**: Handles schema changes in future versions

### Styling (`css/document-approval.css`)
User interface styling:
- **Form styling**: Clean, professional document upload interface
- **Status indicators**: Color-coded status display (green/red/yellow)
- **Admin preview**: Polished document viewing experience
- **Responsive design**: Mobile-friendly layouts and interactions
- **Accessibility**: Proper contrast and keyboard navigation support

### Library Definition (`document_approval.libraries.yml`)
Asset management:
- **CSS inclusion**: Loads styling on relevant pages
- **Version control**: Handles cache busting for updates
- **Performance**: Optimized loading strategies

### Module Hooks (`document_approval.module`)
Drupal integration points:
- **Help system**: Provides module documentation
- **Entity type alterations**: Configures entity permissions
- **User login handling**: Redirects users to application after login
- **Page preprocessing**: Attaches CSS to relevant pages

### Testing (`tests/src/Functional/DocumentApprovalTest.php`)
Quality assurance:
- **Access control testing**: Verifies permission system works correctly
- **Form functionality**: Tests document submission process
- **Admin interface**: Validates administrative features
- **Integration testing**: Ensures all components work together

## Technical Implementation Details

### Entity Design Philosophy
The `document_submission` entity uses a flat structure rather than complex relationships. This design choice prioritizes:
- **Performance**: Fewer database joins for common operations
- **Simplicity**: Easier to understand and maintain
- **Flexibility**: Easy to extend with additional fields

### File Management Strategy
Uses Drupal's managed file system:
- **Security**: Files are properly validated and stored securely
- **Cleanup**: Automatic garbage collection of unused files
- **Integration**: Seamless integration with Drupal's file API

### Caching Strategy
Implements aggressive cache invalidation:
- **Real-time updates**: Admin sees changes immediately
- **Performance**: Caches where appropriate for speed
- **Consistency**: Ensures data integrity across requests

### Status Logic Implementation
Three-tier status system:
- **Individual documents**: pending/approved/rejected per document
- **Overall submission**: calculated based on individual statuses and requirements
- **User experience**: Clear visual feedback at all levels

## Installation & Configuration

### Prerequisites
- Drupal 10.3 or higher
- File module enabled
- User module enabled
- Appropriate file system permissions

### Installation Steps
1. Extract module to `web/modules/custom/document_approval/`
2. Enable via Drush: `drush en document_approval`
3. Configure permissions at `/admin/people/permissions`
4. Set up document types at `/admin/config/document-approval/settings`
5. Test with a user account

### Post-Installation Configuration
- **Document Labels**: Customize names for your specific use case
- **File Restrictions**: Set appropriate file types and size limits
- **Required Documents**: Mark which documents are mandatory
- **User Permissions**: Assign roles appropriately

## Routes & Access Control

### User Routes
- `/application` - Main document submission interface
  - **Access**: Authenticated users with 'submit documents' permission
  - **Functionality**: Upload documents, view status, update submissions
  - **Cache**: Disabled for real-time status updates

- `/application-status` - Dedicated status viewing page
  - **Access**: Authenticated users
  - **Functionality**: View submission progress, congratulations messages
  - **Cache**: Disabled for real-time updates

### Administrative Routes
- `/admin/config/document-approval/settings` - System configuration
  - **Access**: Users with 'administer document approval' permission
  - **Functionality**: Configure document labels, file settings, requirements

- `/admin/document-submissions` - Submissions overview
  - **Access**: Users with 'administer document approval' permission
  - **Functionality**: List all user submissions with status overview

- `/admin/document-submissions/{submission_id}` - Individual review
  - **Access**: Users with 'administer document approval' permission
  - **Functionality**: Review documents, approve/reject, add comments

## Database Schema Details

### Entity Structure
```
document_submission
├── id (Primary Key)
├── user_id (Foreign Key to users table)
├── status (overall: pending/approved/rejected)
├── doc1 (File reference)
├── doc1_status (pending/approved/rejected)
├── doc1_comments (Text, admin feedback)
├── doc2 (File reference)
├── doc2_status (pending/approved/rejected)
├── doc2_comments (Text, admin feedback)
├── doc3 (File reference)
├── doc3_status (pending/approved/rejected)
├── doc3_comments (Text, admin feedback)
├── doc4 (File reference)
├── doc4_status (pending/approved/rejected)
├── doc4_comments (Text, admin feedback)
├── created (Timestamp)
└── changed (Timestamp)
```

### Field Definitions
- **File fields**: Use managed_file with proper validation
- **Status fields**: String fields with limited values
- **Comment fields**: Text fields for admin feedback
- **User reference**: Entity reference to user entity

## Maintenance & Troubleshooting

### Common Issues
- **File upload failures**: Check file permissions and size limits
- **Status not updating**: Clear cache and check entity permissions
- **Access denied**: Verify user roles and permissions
- **Preview not working**: Ensure file module is properly configured

### Performance Considerations
- **File storage**: Monitor disk usage for uploaded documents
- **Database growth**: Plan for entity table growth over time
- **Cache management**: Regular cache clearing may be needed for development

### Security Notes
- **File validation**: All uploads are validated for type and size
- **Access control**: Strict permission checking throughout
- **Data isolation**: Users can only see their own submissions
- **Admin separation**: Clear separation between user and admin interfaces

### Development Notes
- **Code structure**: Follows Drupal coding standards
- **Extensibility**: Easy to add new document types or fields
- **Customization**: Admin settings allow runtime configuration
- **Testing**: Comprehensive test coverage for all major functions

This module provides a production-ready document approval system that can be customized for various organizational needs while maintaining security and performance standards.
