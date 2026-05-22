# Quick Start Guide - Agent Management & Customer Onboarding

## System Overview

This digital system manages the complete customer onboarding workflow with three main user roles:
- **Admin**: Manages agents, views all data
- **Agent**: Reviews customer applications and documents
- **Customer**: Submits applications with personal details and documents

---

## Key Files & Documentation

| Document | Purpose |
|----------|---------|
| `CUSTOMER_ONBOARDING_FLOW.md` | Complete workflow documentation with database schema |
| `API_TESTING_GUIDE.md` | Step-by-step testing guide for entire flow |
| `README.md` | Project setup and installation |
| `TESTING.md` | Unit test guidelines |

---

## Agent Management (Admin Only)

### Create an Agent
**Web:** `/admin/agents/create`

```
Name: Sales Agent Name
Email: agent@company.com
Employee Code: AGT-001
Phone: +1-555-0100
Password: SecurePassword123
Active: ✓
```

**API:**
```
POST /api/v1/admin/agents
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "name": "Sales Agent Name",
  "email": "agent@company.com",
  "employee_code": "AGT-001",
  "phone": "+1-555-0100",
  "password": "SecurePassword123",
  "is_active": true
}
```

### Edit Agent Details
**Web:** `/admin/agents/{id}/edit`

**API:**
```
PUT /api/v1/admin/agents/{agent_id}
Authorization: Bearer {admin_token}

{
  "name": "Updated Name",
  "email": "newemail@company.com",
  "phone": "+1-555-0200"
}
```

### Toggle Agent Status
**Web:** `/admin/agents` → Click "Activate/Deactivate"

**Note:** Deactivated agents CANNOT login

**API:**
```
PATCH /api/v1/admin/agents/{agent_id}/status
Authorization: Bearer {admin_token}

{
  "is_active": false
}
```

**Result:** Agent receives error during login: "Your account has been deactivated"

---

## Customer Journey

### 1. Customer Registration
**Web:** `/register`

```
Name: Customer Name
Email: customer@example.com
Password: Password123
```

### 2. Customer Creates Application
**Web:** `/customer/applications/create`

Fill:
- Personal details
- Select product
- Select agent
- Upload documents (multiple)
- Save as Draft OR Submit

**Status:** `draft` → `submitted`

### 3. Customer Uploads Documents
Can be done during application creation or separately:

**Supported Document Types:**
- Identification Proof
- Address Proof
- Income Certificate
- Employment Letter
- Bank Statement

**File Types:** PDF, JPG, JPEG, PNG

### 4. Customer Views Application Status
**Web:** `/customer/applications`

See:
- Application list with status
- Document count
- Submission date
- Agent assigned
- Quick view/edit buttons

---

## Agent Review Process

### 1. Agent Logs In
**Web:** `/login`

```
Email: agent@company.com
Password: SecurePassword123
```

**If Inactive:** "Your account has been deactivated. Please contact administrator."

### 2. Agent Views Applications
**Web:** `/agent/applications`

Displays:
- Customer applications assigned to this agent
- Application status
- Number of documents
- Submission date

### 3. Agent Reviews Application Details
**Web:** `/agent/applications/{id}`

View:
- Customer personal information
- All uploaded documents
- Document status
- Assigned products

### 4. Agent Approves or Rejects Documents

**Approve Document:**
```
Web: Click "Approve" button on document
- Add optional notes
- Document status → "approved"
- Green badge displayed

API:
POST /api/v1/documents/{doc_id}/approve
Authorization: Bearer {agent_token}
{
  "notes": "Verified successfully"
}
```

**Reject Document:**
```
Web: Click "Reject" button on document
- Add rejection reason (required)
- Document status → "rejected"
- Red badge displayed
- Customer receives notification

API:
POST /api/v1/documents/{doc_id}/reject
Authorization: Bearer {agent_token}
{
  "rejection_reason": "Image quality is poor"
}
```

### 5. Agent Verifies Application
**Requirement:** ALL documents must be "approved"

```
Web: Click "Verify Application" button
- Add notes (optional)
- Application status → "verified"

API:
POST /api/v1/applications/{app_id}/verify
Authorization: Bearer {agent_token}
{
  "notes": "All documents verified"
}
```

### 6. Agent Converts Application
**Requirement:** Application must be "verified"

```
Web: Click "Convert Application" button
- Add notes (optional)
- Set effective date (optional)
- Application status → "converted"

API:
POST /api/v1/applications/{app_id}/convert
Authorization: Bearer {agent_token}
{
  "notes": "Customer approved for conversion",
  "effective_date": "2026-04-22"
}
```

---

## Document Resubmission Flow

### When Document is Rejected

**Customer Receives:**
- In-app notification
- Document shows "Rejected" status
- Rejection reason visible
- "Resubmit Document" button appears

### Customer Resubmits Document

```
Web: 
1. Click "Resubmit Document" button
2. Upload new/corrected file
3. Click "Resubmit"
4. Document status → "uploaded"
5. Sent to agent for review

API:
POST /api/v1/applications/{app_id}/documents/{doc_id}/resubmit
Authorization: Bearer {customer_token}
Content-Type: multipart/form-data

- file: <binary_file>
- notes: "Uploading clearer copy" (optional)
```

### Agent Reviews Resubmitted Document

Same as initial review:
- Approve → "approved"
- Reject → "rejected" (can request another resubmission)

---

## Status Progression

### Application Status Flow
```
┌─────────┐
│ draft   │ (saved, not submitted)
└────┬────┘
     │ (customer submits)
     ↓
┌──────────┐
│submitted │ (agent reviewing)
└────┬─────┘
     │ (all docs approved + agent clicks verify)
     ↓
┌──────────┐
│verified  │ (agent confirms documents)
└────┬─────┘
     │ (agent clicks convert)
     ↓
┌──────────┐
│converted │ (final status, process complete)
└──────────┘
```

### Document Status Flow
```
┌──────────┐
│uploaded  │ (initial upload by customer)
└────┬─────┘
     │
     ├─→ approved ─→ (agent approves)
     │
     └─→ rejected ─→ (agent rejects)
              │
              │ (customer resubmits)
              ↓
           uploaded ─→ (back to review)
```

---

## Database Tables

### Key Tables

**users**
- Stores all user accounts (admin, agent, customer)
- Fields: id, name, email, password, role, created_at

**agent_profiles**
- Extended agent information
- Fields: user_id, employee_code, phone, is_active

**applications**
- Customer applications
- Fields: id, customer_id, agent_user_id, product_id, status, full_name, email, phone, address, date_of_birth, identification_type, identification_number

**documents**
- Application documents
- Fields: id, application_id, document_type, status, file_path, rejection_reason, uploaded_at, verified_at

**customers**
- Customer records
- Fields: id, user_id, phone, full_name, created_at

---

## Common URLs

| Action | URL | Authentication |
|--------|-----|-----------------|
| Register | `/register` | None |
| Login | `/login` | None |
| Customer Dashboard | `/customer/dashboard` | Customer |
| My Applications | `/customer/applications` | Customer |
| New Application | `/customer/applications/create` | Customer |
| View Application | `/customer/applications/{id}` | Customer |
| Edit Application | `/customer/applications/{id}/edit` | Customer |
| Agent Dashboard | `/agent/dashboard` | Agent |
| Agent Applications | `/agent/applications` | Agent |
| View Application | `/agent/applications/{id}` | Agent |
| Admin Dashboard | `/admin/dashboard` | Admin |
| Agents List | `/admin/agents` | Admin |
| Create Agent | `/admin/agents/create` | Admin |
| Edit Agent | `/admin/agents/{id}/edit` | Admin |
| Onboarding Link | `/onboarding/{link_code}` | None |

---

## API Base URL

All API endpoints start with:
```
http://localhost/api/v1
```

---

## API Authentication

### Get Token (Login)
```
POST /api/v1/auth/login

{
  "email": "user@example.com",
  "password": "password123"
}

Response:
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "name": "User Name",
    "role": "agent"
  }
}
```

### Use Token in Requests
```
Authorization: Bearer {token}
```

### Logout
```
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

---

## Response Codes

| Code | Meaning | Example |
|------|---------|---------|
| 200 | Success | Document approved |
| 201 | Created | Application created |
| 400 | Bad Request | Missing required fields |
| 401 | Unauthorized | Invalid token or not logged in |
| 403 | Forbidden | Access denied (permissions) |
| 404 | Not Found | Application not found |
| 422 | Unprocessable | Validation failed |
| 500 | Server Error | Database/server issue |

---

## Testing the Complete Flow

### Scenario 1: Happy Path (5 minutes)
1. Admin creates agent (1 min)
2. Customer registers (30 sec)
3. Customer creates & submits application with 2 documents (2 min)
4. Agent approves all documents (1 min)
5. Agent verifies and converts application (1 min)

### Scenario 2: Document Rejection (3 minutes)
1. Start from "Agent reviews application"
2. Agent rejects one document with reason (30 sec)
3. Customer resubmits document (1 min)
4. Agent approves resubmitted document (30 sec)
5. Agent verifies application (1 min)

### Scenario 3: Agent Deactivation (2 minutes)
1. Admin deactivates agent
2. Verify agent cannot login
3. Verify error message shown
4. Admin reactivates agent
5. Verify agent can login again

---

## Important Notes

### Security
- Deactivated agents are blocked from login immediately
- Customers cannot access other customers' applications
- Agents can only see their assigned applications
- Token expires after 1 hour of inactivity

### Data Validation
- Email must be unique
- Employee code must be unique
- Phone format: Allow +, -, spaces, digits only
- Date of birth must be valid past date
- Identification number required and must match type

### File Uploads
- Maximum file size: 10 MB
- Allowed formats: PDF, JPG, JPEG, PNG
- Files stored securely in `/storage/documents/`
- Files are not directly accessible via URL

### Notifications
- Customers notified when document is approved/rejected
- Customers notified when application status changes
- Agents notified when new application submitted
- Agents notified when document is resubmitted

---

## Troubleshooting

### Agent Cannot Login
**Check:**
1. Is agent active? (Web: `/admin/agents`)
2. Is password correct?
3. Is email correct?

**Solution:** Admin can reactivate if deactivated

### Document Upload Fails
**Check:**
1. Is file less than 10 MB?
2. Is format PDF, JPG, PNG?
3. Is customer logged in?

**Solution:** Try different file or check browser console for errors

### Application Not Visible to Agent
**Check:**
1. Is application submitted (not draft)?
2. Is agent still active?
3. Is agent assigned to this application?

**Solution:** Check application's agent assignment

### Cannot Verify Application
**Check:**
1. Are all documents approved?
2. Is application status "submitted"?

**Solution:** Ensure all documents have green "approved" badge

---

## Development Resources

### View PHP Artisan Commands
```
php artisan list
```

### Run Tests
```
./vendor/bin/phpunit
```

### Generate New Migration
```
php artisan make:migration create_table_name
```

### Clear Cache
```
php artisan cache:clear
php artisan config:clear
```

---

## Support & Next Steps

1. **Read Full Docs:** Start with `CUSTOMER_ONBOARDING_FLOW.md`
2. **Test API:** Use `API_TESTING_GUIDE.md` for step-by-step examples
3. **Import Postman:** Use `Kanban_Agent_API.postman_collection.json`
4. **Check Code:** Review model relationships in `app/Models/`
5. **Run Tests:** Execute `./vendor/bin/phpunit` to verify setup

---

## Quick Command Reference

### Start Development Server
```
php artisan serve
```

### Database Migrations
```
php artisan migrate
php artisan migrate:rollback
php artisan migrate:refresh
```

### Create Admin User
```
php artisan tinker
>>> User::create(['name'=>'Admin','email'=>'admin@test.com','password'=>Hash::make('password'),'role'=>'admin'])
```

### Check Logs
```
tail -f storage/logs/laravel.log
```

---

## Next Development Tasks

- [ ] Create mobile app for agent application review
- [ ] Add email notifications for status changes
- [ ] Implement bulk document upload (ZIP)
- [ ] Add OCR for document verification
- [ ] Create agent performance dashboard
- [ ] Add SMS notifications
- [ ] Implement document signing
- [ ] Add customer portal redesign
- [ ] Create advanced reporting
- [ ] Add API rate limiting

---

**For detailed information, refer to the complete documentation files provided.**
