# Admin Panel Documentation

## Overview

A fully-featured Laravel Blade-based admin dashboard for managing the Digital System. The admin panel provides complete data management capabilities with role-based access control, real-time reporting, and audit logging.

## Access & Credentials

### Login URL
```
http://localhost:8000/login
```

### Demo Admin Account
- **Email**: `admin@digital-system.test`
- **Password**: `password`

## Features

### 1. Dashboard
**Route**: `/admin/dashboard`

Main overview page with:
- Total applications count
- Converted applications count
- Pending review applications
- Conversion rate (%)
- Active agents count
- Total customers count
- Recent applications feed
- Application status distribution chart

**What you can do:**
- View real-time KPIs
- Quick access to recent applications
- Visual status breakdown

### 2. Customers Management
**Route**: `/admin/customers`

Manage all customers in the system.

**Features:**
- List all customers with pagination (15 per page)
- Search by name, email, or phone
- Filter by status (draft, submitted, verified, converted)
- Create new customers
- Edit customer details
- Assign agents to customers
- View customer submission status

**Access pages:**
- List: `/admin/customers`
- Create: `/admin/customers/create`
- Edit: `/admin/customers/{id}/edit`

### 3. Applications Management
**Route**: `/admin/applications`

Complete application lifecycle management.

**Features:**
- List all applications with full details
- Search by customer name or email
- Filter by:
  - Application status (draft, submitted, verified, converted)
  - Product type
- View detailed application view with:
  - Customer information
  - Application timeline
  - Profile payload (submitted data)
  - Attached documents
- **Verify applications** (submitted → verified)
- **Convert applications** (verified → converted)
- View associated documents

**Actions:**
- **Verify**: Moves application from "submitted" to "verified" status
- **Convert**: Moves application from "verified" to "converted" status, marking customer as converted

**Status Flow:**
```
Draft → Submitted → Verified → Converted
```

### 4. Document Management & Review
**Route**: `/admin/documents`

Quality control for submitted documents.

**Features:**
- List all documents across all applications
- Filter by document status:
  - Uploaded (pending review)
  - Approved (verified)
  - Rejected (failed verification)
- Document review interface with:
  - Document preview
  - Download document file
  - Approve/Reject options
  - Add review notes
  - Track reviewer and timestamp

**Review Actions:**
- **Approve**: Document passes verification with optional notes
- **Reject**: Document fails verification (requires reason note)

**Document Statuses:**
- `uploaded` - Newly uploaded, awaiting review
- `approved` - Passed verification
- `rejected` - Failed verification

### 5. Reports & Analytics
**Route**: `/admin/reports`

Comprehensive reporting and analytics dashboard.

**Available Reports:**

#### Conversion Metrics
- Total applications in date range
- Breakdown by status (submitted, verified, converted, draft)
- Overall conversion rate %
- Submission rate %
- Customizable date range (start_date, end_date)

#### Agent Performance
- List of all agents ranked by performance
- Total applications per agent
- Number of converted customers
- Individual conversion rate %
- Sorted by conversion rate (highest first)

#### Product-wise Report
- Applications grouped by product
- Status breakdown per product (draft, submitted, verified, converted)
- Visual progress indicators
- Date range filtering

**Date Filtering:**
All reports can be filtered by:
- Start Date (default: 1 month ago)
- End Date (default: today)

### 6. Audit Logs
**Route**: `/admin/audit-logs`

Complete audit trail for compliance and debugging.

**Shows:**
- All system actions with timestamps
- User who performed the action
- Action type (created, submitted, verified, converted, etc.)
- Entity type (Application, Document, Customer)
- Entity ID
- Metadata (JSON details of what changed)

**Filter by:**
- Action type (select distinct actions)
- Entity type (Application, Document, etc.)

**Typical Actions Logged:**
- `created` - New entity created
- `submitted` - Application submitted
- `verified` - Application verified
- `converted` - Application converted
- `document_uploaded` - File uploaded
- `document_reviewed` - Document reviewed

### 7. Users Management
**Route**: `/admin/users`

Manage system users and roles.

**Features:**
- List all users with pagination
- View user details:
  - Name
  - Email
  - Role (Admin, Agent, Customer)
  - Created date
- Create new users
- Edit existing users
- Delete users (cannot delete own account)
- Assign roles

**User Roles:**
- **Admin**: Full system access, all management functions
- **Agent**: Can manage own customers and applications, access reports of own work
- **Customer**: Can view and track own applications

**Create/Edit User:**
- Full Name (required)
- Email (required, must be unique)
- Password (required on creation, 8+ characters)
- Role (required: Admin, Agent, or Customer)

## Data Management Operations

### Creating a Customer
1. Go to **Customers** → **Add Customer**
2. Enter:
   - Full Name
   - Email (unique)
   - Phone (unique)
   - Assign Agent
3. Click **Create Customer**

### Creating an Application
1. Go to **Applications**
2. (Admin creates application for customer)
3. Select customer, product
4. Save as Draft
5. Submit when ready

### Document Review Workflow
1. Go to **Documents**
2. Filter by status: "uploaded"
3. Click **Review** on document
4. Download and examine file
5. Either:
   - **Approve** with optional notes
   - **Reject** with required reason

### Application Verification Flow
1. Go to **Applications**
2. Filter by status: "submitted"
3. Click **View** on application
4. Review all details and documents
5. Click **Verify Application**
   - Moves to "verified" status
   - Customer status updates to "verified"

### Application Conversion
1. Go to **Applications**
2. Filter by status: "verified"
3. Click **View** on application
4. Click **Convert to Customer**
   - Moves to "converted" status
   - Customer status updates to "converted"
   - Application is now a successful conversion

## Database Schema

### Main Tables Used by Admin Panel

**users**
- Stores system users (admins, agents, customers)
- Fields: id, name, email, password, role, created_at

**customers**
- Stores customer information
- Fields: id, agent_user_id, full_name, phone, email, status, submitted_at, created_at
- Statuses: draft, submitted, verified, converted

**applications**
- Stores application data
- Fields: id, customer_id, agent_user_id, product_id, status, profile_payload (JSON), submitted_at, verified_at, converted_at

**documents**
- Stores uploaded documents
- Fields: id, application_id, document_type, file_path, status, review_note, reviewed_by, reviewed_at

**audit_logs**
- Complete action history
- Fields: id, user_id, action, entity_type, entity_id, meta (JSON), created_at

**products**
- Available loan products
- Fields: id, name, code, description, is_active

## Performance Tips

### Optimize Dashboard Loading
- Dashboard loads with 5 most recent applications
- Use date filters on reports to reduce data
- Search before filtering when possible

### Batch Operations
- Import customers via bulk upload (future enhancement)
- Email batch notifications (future enhancement)

### Caching
- Consider caching product list
- Cache agent performance reports (regenerated weekly)

## Security Considerations

### Access Control
- All admin routes require `auth` middleware
- Role-based access: `role:admin` middleware ensures only admins can access
- Cannot delete own user account
- All actions logged in audit_logs

### Data Protection
- Passwords hashed with bcrypt
- Email and phone unique constraints enforce data integrity
- Soft deletes not implemented (hard deletes are logged)
- File uploads stored with security

### Audit Trail
- Every critical action logged with user ID
- Action metadata captured for analysis
- Timestamp recorded for forensics
- Supports compliance audits

## Troubleshooting

### Can't Access Admin Panel
- Verify you're logged in with admin account
- Check role is set to "admin" in users table
- Refresh cache: `php artisan cache:clear`

### Documents Not Appearing
- Verify documents have `status = 'uploaded'`
- Check file permissions in storage/app/public/documents
- Run: `php artisan storage:link` if needed

### Applications Not Showing in Reports
- Verify date range includes application dates
- Check application status is not "draft"
- Verify agent is assigned correctly

### Audit Logs Not Recording
- Check `php artisan queue:work` is running (if async jobs enabled)
- Verify audit_logs table has correct permissions
- Check application logs: `storage/logs/laravel.log`

## Development & Customization

### Add New Report
1. Add method to `AdminController@reports()`
2. Create view in `resources/views/admin/reports/`
3. Add route to `routes/web.php`

### Add New Management Section
1. Create controller method
2. Create Blade views
3. Add routes to `routes/web.php`
4. Update sidebar navigation in `resources/views/layouts/admin.blade.php`

### Customize Styling
- Edit `resources/views/layouts/admin.blade.php`
- Bootstrap 5 classes available
- Custom CSS in `<style>` section

## API Integration

The admin panel uses the same API endpoints as the REST API:

```
GET  /api/v1/customers
POST /api/v1/customers
GET  /api/v1/applications
POST /api/v1/applications/{id}/verify
POST /api/v1/applications/{id}/convert
GET  /api/v1/documents
POST /api/v1/documents/{id}/review
GET  /api/v1/reports/*
```

## File Structure

```
resources/views/
├── admin/
│   ├── dashboard.blade.php
│   ├── customers/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   ├── applications/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── documents/
│   │   ├── index.blade.php
│   │   └── review.blade.php
│   ├── reports/
│   │   └── index.blade.php
│   ├── audit-logs/
│   │   └── index.blade.php
│   └── users/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
└── layouts/
    └── admin.blade.php
```

## Support & Future Enhancements

**Possible Future Features:**
- Email notification system
- SMS alerts for pending applications
- Bulk customer import
- Advanced reporting with charts
- Data export (CSV, PDF)
- Mobile app admin interface
- Webhook notifications
- Custom workflows
- Template-based approvals

---

**Last Updated**: April 17, 2026  
**Version**: 1.0 (MVP)  
**Status**: Production Ready
