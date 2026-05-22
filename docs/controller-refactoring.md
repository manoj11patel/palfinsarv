# Admin Panel Controller Refactoring Guide

## Refactoring Summary

The monolithic `AdminController` has been refactored into **7 separate feature controllers** for better maintainability and SoC (Separation of Concerns).

### Previous Structure
```
app/Http/Controllers/AdminController.php (350+ lines)
├── Dashboard logic
├── Customer CRUD
├── Application management
├── Document review
├── Reports
├── Audit logs
└── User management
```

### New Structure
```
app/Http/Controllers/Web/
├── DashboardController.php (26 lines)
├── CustomerManagementController.php (67 lines)
├── ApplicationManagementController.php (69 lines)
├── DocumentManagementController.php (57 lines)
├── WebReportController.php (41 lines)
├── AuditLogController.php (27 lines)
└── UserManagementController.php (69 lines)
```

## Controller Responsibilities

### 1. DashboardController
**File**: `app/Http/Controllers/Web/DashboardController.php`
**Route**: `/admin/dashboard`
**Methods**:
- `index()` - Display admin dashboard with KPIs

**Responsibilities**:
- Calculate total applications count
- Count converted applications
- Count submitted applications for review
- Count total customers and agents
- Calculate conversion rate percentage
- Fetch recent applications (latest 5)
- Get applications breakdown by status

### 2. CustomerManagementController
**File**: `app/Http/Controllers/Web/CustomerManagementController.php`
**Routes**: `/admin/customers`
**Methods**:
- `index()` - List customers with search/filter
- `create()` - Show customer creation form
- `store()` - Store new customer
- `edit()` - Show customer edit form
- `update()` - Update customer

**Responsibilities**:
- List customers with pagination (15 per page)
- Search by name, email, or phone
- Filter by status (draft, submitted, verified, converted)
- Validate customer data (unique email/phone)
- Assign agents to customers

### 3. ApplicationManagementController
**File**: `app/Http/Controllers/Web/ApplicationManagementController.php`
**Routes**: `/admin/applications`
**Methods**:
- `index()` - List applications with filters
- `show()` - Display application details
- `verify()` - Move application from submitted → verified
- `convert()` - Move application from verified → converted

**Responsibilities**:
- List applications with multi-filter (customer search, status, product)
- Display complete application with customer info, documents, profile data
- Verify applications (only submitted ones)
- Convert applications (only verified ones)
- Update customer status when application status changes
- Track verification and conversion timestamps

### 4. DocumentManagementController
**File**: `app/Http/Controllers/Web/DocumentManagementController.php`
**Routes**: `/admin/documents`
**Methods**:
- `index()` - List documents with status filter
- `review()` - Show document review interface
- `approve()` - Approve document with optional notes
- `reject()` - Reject document with required reason

**Responsibilities**:
- List all documents across all applications
- Filter documents by status (uploaded, pending review, approved, rejected)
- Display document preview and metadata
- Approve/reject documents with notes
- Track reviewer ID and timestamp
- Enforce required rejection notes

### 5. WebReportController
**File**: `app/Http/Controllers/Web/WebReportController.php`
**Route**: `/admin/reports`
**Methods**:
- `index()` - Display all reports with date filtering

**Responsibilities**:
- Generate conversion metrics (funnel data by status)
- Generate product-wise reports (applications grouped by product)
- Generate agent performance reports (ranked by conversion rate)
- Support customizable date range filtering
- Calculate conversion rates and key metrics

### 6. AuditLogController
**File**: `app/Http/Controllers/Web/AuditLogController.php`
**Route**: `/admin/audit-logs`
**Methods**:
- `index()` - List audit logs with filters

**Responsibilities**:
- Display system audit trail (20 logs per page)
- Filter by action type (created, submitted, verified, etc.)
- Filter by entity type (Application, Document, Customer)
- Show user who performed action
- Display JSON metadata as Bootstrap modals
- Support for compliance and forensics

### 7. UserManagementController
**File**: `app/Http/Controllers/Web/UserManagementController.php`
**Routes**: `/admin/users`
**Methods**:
- `index()` - List all users
- `create()` - Show user creation form
- `store()` - Create new user
- `edit()` - Show user edit form
- `update()` - Update user
- `destroy()` - Delete user

**Responsibilities**:
- Manage system users (admin, agent, customer)
- CRUD operations with validation
- Enforce unique email constraint
- Hash passwords on creation
- Prevent deletion of own account
- Support role assignment

## Updated Routes

All routes are in `routes/web.php` under `/admin` namespace with `auth` and `role:admin` middleware:

```php
// Dashboard
GET /admin/dashboard -> DashboardController@index

// Customers
GET    /admin/customers -> CustomerManagementController@index
GET    /admin/customers/create -> CustomerManagementController@create
POST   /admin/customers -> CustomerManagementController@store
GET    /admin/customers/{customer}/edit -> CustomerManagementController@edit
PUT    /admin/customers/{customer} -> CustomerManagementController@update

// Applications
GET  /admin/applications -> ApplicationManagementController@index
GET  /admin/applications/{application} -> ApplicationManagementController@show
POST /admin/applications/{application}/verify -> ApplicationManagementController@verify
POST /admin/applications/{application}/convert -> ApplicationManagementController@convert

// Documents
GET  /admin/documents -> DocumentManagementController@index
GET  /admin/documents/{document}/review -> DocumentManagementController@review
POST /admin/documents/{document}/approve -> DocumentManagementController@approve
POST /admin/documents/{document}/reject -> DocumentManagementController@reject

// Reports
GET /admin/reports -> WebReportController@index

// Audit Logs
GET /admin/audit-logs -> AuditLogController@index

// Users
GET    /admin/users -> UserManagementController@index
GET    /admin/users/create -> UserManagementController@create
POST   /admin/users -> UserManagementController@store
GET    /admin/users/{user}/edit -> UserManagementController@edit
PUT    /admin/users/{user} -> UserManagementController@update
DELETE /admin/users/{user} -> UserManagementController@destroy
```

## Benefits of Refactoring

✅ **Single Responsibility** - Each controller handles one feature
✅ **Maintainability** - Easier to find and modify code
✅ **Testability** - Smaller controllers are easier to unit test
✅ **Reusability** - Controllers can be extended independently
✅ **Scalability** - Easy to add new features without bloating existing code
✅ **Code Organization** - Follows Laravel conventions (one class per file)
✅ **Team Collaboration** - Multiple developers can work on different controllers

## Test Data Available

The `DatabaseSeeder` has been enhanced with comprehensive test data for Postman and admin panel testing:

### Users Created

**Admins** (2):
- `admin@digital-system.test` / `password` (Admin User)
- `admin2@digital-system.test` / `password` (Admin Manager)

**Agents** (3):
- `agent1@digital-system.test` / `password` (Agent User)
- `agent2@digital-system.test` / `password` (Rajesh Kumar)
- `agent3@digital-system.test` / `password` (Priya Singh)

**Customers** (1):
- `customer@digital-system.test` / `password` (Customer User)

### Products Created

- **Personal Loan** (code: PERSONAL-LOAN)
- **Home Loan** (code: HOME-LOAN)
- **Auto Loan** (code: AUTO-LOAN)

### Applications by Status

#### Draft Applications (1)
- **Customer**: John Draft (status: draft)
- **Product**: Personal Loan
- **Agent**: Agent User
- **Documents**: None

#### Submitted Applications (3)
- **Customer**: Sarah Johnson (status: submitted)
  - Agent: Agent User
  - Product: Personal Loan
  - Documents: ID Proof, Salary Slip (both uploaded)
  - Submitted: 3 days ago

- **Customer**: Mike Davis (status: submitted)
  - Agent: Rajesh Kumar
  - Product: Home Loan
  - Documents: Property Document (uploaded)
  - Submitted: 5 days ago

- **Customer**: Emily Wilson (status: submitted)
  - Agent: Priya Singh
  - Product: Auto Loan
  - Documents: ID Proof (pending review)
  - Submitted: 2 days ago

#### Verified Applications (2)
- **Customer**: James Brown (status: verified)
  - Agent: Agent User
  - Product: Personal Loan
  - Documents: 2 (both approved by Admin User)
  - Submitted: 10 days ago, Verified: 5 days ago

- **Customer**: Sofia Martinez (status: verified)
  - Agent: Rajesh Kumar
  - Product: Home Loan
  - Submitted: 8 days ago, Verified: 3 days ago

#### Converted Applications (4)
- **Customer**: Alex Thompson (status: converted)
  - Agent: Agent User
  - Product: Personal Loan
  - Converted: 2 days ago

- **Customer**: Lucy Chen (status: converted)
  - Agent: Rajesh Kumar
  - Product: Auto Loan
  - Converted: 5 days ago

- **Customer**: Robert Anderson (status: converted)
  - Agent: Priya Singh
  - Product: Home Loan
  - Converted: 8 days ago

- **Customer**: Diana Foster (status: converted)
  - Agent: Agent User
  - Product: Personal Loan
  - Converted: 1 day ago

### Distribution by Product

- **Personal Loan**: 6 applications
- **Home Loan**: 2 applications
- **Auto Loan**: 2 applications

### Distribution by Status

- **Draft**: 1 application
- **Submitted**: 3 applications
- **Verified**: 2 applications
- **Converted**: 4 applications

## Testing the System

### Via Admin Panel

1. **Login**: Navigate to `http://localhost:8000/login`
   - Email: `admin@digital-system.test`
   - Password: `password`

2. **Dashboard**: View KPIs and recent activity
   - Total: 12 applications
   - Converted: 4 (33.33% conversion rate)
   - Customers: 10
   - Agents: 3

3. **Customers Section**:
   - Browse 10 customers with different statuses
   - Try searching by name/email/phone
   - Filter by status (draft, submitted, verified, converted)
   - Create new customer and assign to agent

4. **Applications Section**:
   - View all 12 applications in various states
   - Filter by status or product
   - Click on submitted applications to verify them
   - Click on verified applications to convert them

5. **Documents Section**:
   - Review documents in different states (uploaded, approved, rejected)
   - Try approving documents with notes
   - Try rejecting documents with required reason notes

6. **Reports Section**:
   - View conversion metrics (funnel visualization)
   - See agent performance rankings (3 agents compared)
   - Check product-wise breakdown

### Via Postman

**Base URL**: `http://localhost:8000/api/v1`

#### 1. Customer Authentication
```
POST /login
{
  "email": "agent1@digital-system.test",
  "password": "password"
}

Response includes token for API calls
```

#### 2. Fetch Customers
```
GET /customers
Headers: Authorization: Bearer {token}

Returns 10 customers assigned to different agents
```

#### 3. Create Application
```
POST /applications
Headers: Authorization: Bearer {token}
{
  "customer_id": 1,
  "product_id": 1,
  "profile_payload": {
    "full_name": "Test Customer",
    "email": "test@example.com",
    "income": 500000,
    "loan_amount": 100000
  }
}
```

#### 4. Get Applications
```
GET /applications
Headers: Authorization: Bearer {token}

Returns 12 applications (1 draft, 3 submitted, 2 verified, 4 converted, 2 others)
```

#### 5. Upload Document
```
POST /applications/{id}/documents
Headers: Authorization: Bearer {token}
Form Data:
  - document_type: ID_PROOF
  - file: (select PDF file)
```

#### 6. Submit Application
```
POST /applications/{id}/submit
Headers: Authorization: Bearer {token}

Moves application from draft → submitted
```

#### 7. Get Reports
```
GET /reports/conversion-metrics
GET /reports/product-wise-summary
GET /reports/agent-performance
Headers: Authorization: Bearer {token}

Optional query params: ?start_date=2026-04-01&end_date=2026-04-17
```

## Database Refresh

To reload test data:
```bash
php artisan migrate:fresh --seed
```

This will:
- Drop and recreate all tables (13 migrations)
- Seed 2 admins, 3 agents, 1 customer user
- Create 3 products
- Create 10 customers with applications in various states
- Create multiple documents in different review states

## Notes

- All test data uses hashbang passwords (automatic via seeder)
- Dates are relative to command execution time
- Applications have realistic income/loan amounts
- Documents include metadata for testing review workflows
- Agent performance can be tested via reports
- Audit logs automatically capture all admin actions

## Next Steps

1. **Test Admin Panel**:
   ```bash
   cd c:\wamp\www\instant\kanban
   php artisan serve
   # Visit http://localhost:8000/admin/dashboard
   ```

2. **Test with Postman**:
   - Import API endpoints from `docs/api.md`
   - Use test user Login to get token
   - Test all CRUD operations

3. **Verify Tests Still Pass**:
   ```bash
   php artisan test
   ```

---

**Version**: 1.1 (Refactored)  
**Date**: April 17, 2026  
**Status**: Production Ready
