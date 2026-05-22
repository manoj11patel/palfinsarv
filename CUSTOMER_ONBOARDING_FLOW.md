# Customer Onboarding Flow - Complete Testing Documentation

## Overview
This document provides a complete guide for the customer onboarding workflow including admin operations, customer journey, agent review process, and all relevant API endpoints.

---

## ADMIN SIDE - Agent Management

### 1. Create Agent (Admin Operation)

**Flow:**
1. Admin logs in → Navigate to `/admin/agents`
2. Click "Add Agent" button
3. Fill in agent details:
   - Name
   - Email
   - Employee Code (unique)
   - Phone
   - Password
   - Set Active status (checkbox)
4. Click "Create Agent" → Agent is created

**Web Interface:** `POST /admin/agents`
- Route: `admin.agents.store`
- Response: Redirect to agents list with success message

**Database Impact:**
- Creates user with role='agent'
- Creates agent_profile record with is_active=true/false

---

## CUSTOMER SIDE - Onboarding Flow

### Phase 1: Customer Registration/Access

#### Option A: Customer Self-Registers
**Flow:**
1. Customer visits `/register`
2. Fills registration form:
   - Name
   - Email
   - Password
3. Clicks "Register" → Account created as 'customer' role
4. Redirected to customer dashboard

**API Endpoint:** `POST /register`
- No API token required (public endpoint)
- Request body:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```
- Response: Redirects to dashboard

#### Option B: Customer Accesses via Agent's Onboarding Link
**Flow:**
1. Agent generates onboarding link (from agent dashboard)
2. Customer receives link: `{base_url}/onboarding/UNIQUE_LINK_CODE`
3. Customer clicks link
4. System checks if customer exists:
   - If new: Shows registration form pre-filled with agent
   - If existing: Shows application form directly

**API Endpoint:** `GET /api/v1/onboarding/{link_code}`
- No authentication required
- Response:
```json
{
  "success": true,
  "agent_id": 1,
  "agent_name": "Agent Name",
  "products": [...]
}
```

---

### Phase 2: Submit Personal Details & Documents

**Flow:**
1. Customer logs in → Customer Dashboard
2. Click "Submit Application" or "New Application"
3. Fill Personal Details:
   - Full Name
   - Email
   - Phone
   - Address
   - Date of Birth
   - Identification Number
   - Select Product(s)
   - Select Agent (if not pre-selected)
4. Upload Required Documents:
   - Identification Proof
   - Address Proof
   - Income Certificate (optional)
   - Other required docs based on product
5. Save as Draft OR Submit Application

**Web Interface:** 
- Route: `POST /customer/applications` (save draft)
- Route: `POST /customer/applications/{id}/submit` (submit)

**API Endpoints:**

**Create/Update Application:**
```
POST /api/v1/applications
Content-Type: application/json
Authorization: Bearer {customer_token}

Request Body:
{
  "full_name": "John Doe",
  "email": "john@example.com",
  "phone": "9876543210",
  "address": "123 Main St",
  "date_of_birth": "1990-01-15",
  "identification_number": "ID123456",
  "products": [1, 2],
  "agent_user_id": 5,
  "status": "draft"
}

Response (201):
{
  "id": 1,
  "status": "draft",
  "documents": [],
  "created_at": "2026-04-22T10:00:00Z"
}
```

**Upload Documents:**
```
POST /api/v1/applications/{application_id}/documents
Content-Type: multipart/form-data
Authorization: Bearer {customer_token}

Request Body:
{
  "document_type": "identification_proof",
  "file": <binary_file>,
  "description": "Passport copy"
}

Response (201):
{
  "id": 1,
  "document_type": "identification_proof",
  "status": "uploaded",
  "file_path": "/storage/documents/app_1_doc_1.pdf",
  "uploaded_at": "2026-04-22T10:05:00Z"
}
```

**Submit Application:**
```
POST /api/v1/applications/{application_id}/submit
Authorization: Bearer {customer_token}

Response (200):
{
  "id": 1,
  "status": "submitted",
  "submitted_at": "2026-04-22T10:10:00Z",
  "message": "Application submitted successfully"
}
```

**Database Impact:**
- Creates `applications` record with status='draft' or 'submitted'
- Creates `documents` records with status='uploaded'
- Links to customer_id and agent_user_id

---

## AGENT SIDE - Application Review

### Phase 3: Agent Reviews Application

**Flow:**
1. Agent logs in → Agent Dashboard
2. Navigate to "My Applications" or "Pending Applications"
3. View list of applications assigned to agent
4. Click on application to view details:
   - Customer personal details
   - All uploaded documents
   - Product details
5. Review each document
6. Add internal notes/comments

**Web Interface:**
- Route: `/agent/applications` (list)
- Route: `/agent/applications/{id}` (view details)

**API Endpoints:**

**List Agent's Applications:**
```
GET /api/v1/applications?status=submitted
Authorization: Bearer {agent_token}

Response (200):
[
  {
    "id": 1,
    "customer": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "9876543210"
    },
    "status": "submitted",
    "documents": [
      {
        "id": 1,
        "type": "identification_proof",
        "status": "uploaded"
      }
    ],
    "submitted_at": "2026-04-22T10:10:00Z"
  }
]
```

**Get Application Details:**
```
GET /api/v1/applications/{application_id}
Authorization: Bearer {agent_token}

Response (200):
{
  "id": 1,
  "customer": {
    "id": 1,
    "full_name": "John Doe",
    "email": "john@example.com",
    "phone": "9876543210",
    "address": "123 Main St",
    "date_of_birth": "1990-01-15",
    "identification_number": "ID123456"
  },
  "products": [...],
  "status": "submitted",
  "documents": [
    {
      "id": 1,
      "type": "identification_proof",
      "status": "uploaded",
      "file_path": "/storage/documents/app_1_doc_1.pdf"
    }
  ],
  "submitted_at": "2026-04-22T10:10:00Z"
}
```

**Get Document for Review:**
```
GET /api/v1/documents/{document_id}
Authorization: Bearer {agent_token}

Response (200):
{
  "id": 1,
  "application_id": 1,
  "type": "identification_proof",
  "status": "uploaded",
  "file_path": "/storage/documents/app_1_doc_1.pdf",
  "file_url": "{base_url}/storage/documents/app_1_doc_1.pdf",
  "uploaded_at": "2026-04-22T10:05:00Z"
}
```

---

### Phase 4: Agent Verifies/Rejects Documents

#### Option A: Approve Document
**Flow:**
1. Agent reviews document in application details
2. Clicks "Approve" button on document
3. Document status changes to "approved"
4. Agent can now verify entire application

**API Endpoint:**
```
POST /api/v1/documents/{document_id}/approve
Authorization: Bearer {agent_token}

Request Body:
{
  "notes": "Document verified successfully"
}

Response (200):
{
  "id": 1,
  "status": "approved",
  "verified_at": "2026-04-22T10:15:00Z",
  "verified_by_agent": "Agent Name"
}
```

#### Option B: Reject Document
**Flow:**
1. Agent reviews document and finds issues
2. Clicks "Reject" button
3. Provides rejection reason
4. Customer receives notification to resubmit
5. Document status changes to "rejected"

**API Endpoint:**
```
POST /api/v1/documents/{document_id}/reject
Authorization: Bearer {agent_token}

Request Body:
{
  "rejection_reason": "Document is not clear. Please upload a better quality image."
}

Response (200):
{
  "id": 1,
  "status": "rejected",
  "rejection_reason": "Document is not clear. Please upload a better quality image.",
  "rejected_at": "2026-04-22T10:15:00Z"
}
```

**Database Impact:**
- Updates `documents` record with status='approved' or 'rejected'
- Stores agent_id who performed the action
- Stores timestamp and rejection_reason (if rejected)

---

### Phase 5: Agent Verifies Entire Application

**Flow:**
1. Agent reviews all details and documents
2. All documents must be 'approved' to verify application
3. Agent clicks "Verify Application"
4. Application status changes to "verified"
5. Customer sees application as "Verified"

**API Endpoint:**
```
POST /api/v1/applications/{application_id}/verify
Authorization: Bearer {agent_token}

Request Body:
{
  "notes": "All documents verified. Customer approved for next stage."
}

Response (200):
{
  "id": 1,
  "status": "verified",
  "verified_at": "2026-04-22T10:20:00Z",
  "verified_by_agent": "Agent Name"
}
```

**Database Impact:**
- Updates `applications` record with status='verified'
- Stores agent_id and timestamp

---

### Phase 6: Agent Converts Application

**Flow:**
1. After application is "verified"
2. Agent reviews final decision
3. Agent clicks "Convert to Customer" or "Accept Application"
4. Application status changes to "converted"
5. Customer becomes active customer in system
6. Customer receives confirmation notification

**API Endpoint:**
```
POST /api/v1/applications/{application_id}/convert
Authorization: Bearer {agent_token}

Request Body:
{
  "notes": "Customer approved for conversion",
  "effective_date": "2026-04-22"
}

Response (200):
{
  "id": 1,
  "status": "converted",
  "converted_at": "2026-04-22T10:25:00Z",
  "converted_by_agent": "Agent Name"
}
```

**Database Impact:**
- Updates `applications` record with status='converted'
- May create customer account or link to existing customer
- Stores conversion timestamp

---

## CUSTOMER SIDE - Resubmit Rejected Document

**Flow:**
1. Customer receives notification: "Document XYZ rejected - Reason: Document is not clear..."
2. Customer logs in to dashboard
3. Views application with rejected document
4. Clicks "Resubmit Document"
5. Uploads new/corrected document
6. Submits for re-review
7. Agent reviews again - can approve or reject again

**API Endpoint:**

**Get Customer's Applications:**
```
GET /api/v1/customer/applications
Authorization: Bearer {customer_token}

Response (200):
[
  {
    "id": 1,
    "status": "verified",
    "documents": [
      {
        "id": 1,
        "type": "identification_proof",
        "status": "approved"
      },
      {
        "id": 2,
        "type": "address_proof",
        "status": "rejected",
        "rejection_reason": "Document is not clear..."
      }
    ]
  }
]
```

**Resubmit Document:**
```
POST /api/v1/applications/{application_id}/documents/{document_id}/resubmit
Content-Type: multipart/form-data
Authorization: Bearer {customer_token}

Request Body:
{
  "file": <binary_file>,
  "notes": "Uploading clearer copy"
}

Response (201):
{
  "id": 3,
  "application_id": 1,
  "document_type": "address_proof",
  "status": "uploaded",
  "previous_status": "rejected",
  "uploaded_at": "2026-04-22T11:00:00Z"
}
```

**Database Impact:**
- Creates new `documents` record for resubmitted document
- Marks previous document as 'archived' or 'superseded'
- Document status goes back to 'uploaded' for re-review

---

## Complete Testing Workflow

### Test Case 1: Happy Path (All Documents Approved)

**Step 1: Admin Setup**
```
1. Admin logs in → /admin
2. Navigate to /admin/agents
3. Click "Add Agent"
4. Fill:
   - Name: "Test Agent"
   - Email: "agent@example.com"
   - Employee Code: "AGT001"
   - Phone: "9876543210"
   - Password: "password123"
   - Active: checked
5. Click Create
Expected: Agent created, visible in list
```

**Step 2: Customer Registration**
```
1. Visit /register
2. Fill:
   - Name: "Test Customer"
   - Email: "customer@example.com"
   - Password: "password123"
3. Click Register
Expected: Redirected to /customer/dashboard
```

**Step 3: Customer Creates Application**
```
1. From dashboard, click "Submit Application"
2. Fill personal details:
   - Full Name: "Test Customer"
   - Email: "customer@example.com"
   - Phone: "9123456789"
   - Address: "123 Test Street"
   - Date of Birth: "1990-01-15"
   - Identification: "ID123456"
   - Product: Select any product
   - Agent: Select "Test Agent"
3. Click Save Draft
Expected: Application saved with status='draft'

OR

3. Click Submit
Expected: Application submitted with status='submitted'
```

**Step 4: Customer Uploads Documents**
```
1. In application details, click "Upload Document"
2. Select document type: "Identification Proof"
3. Choose file (PDF/image)
4. Click Upload
Expected: Document uploaded, status='uploaded'

Repeat for other required documents:
- Address Proof
- Income Certificate (if required)
```

**Step 5: Agent Reviews Application**
```
1. Agent logs in → /agent
2. Click "My Applications"
3. Click on customer application
4. Review customer details
5. Review each document
Expected: Can see all details and documents
```

**Step 6: Agent Approves Documents**
```
1. On application detail, find "Identification Proof"
2. Click "Approve" button
3. Enter notes: "Document verified"
4. Click Confirm
Expected: Document status changes to 'approved'

Repeat for all documents
```

**Step 7: Agent Verifies Application**
```
1. Once all documents are 'approved'
2. Click "Verify Application" button
3. Enter notes: "All verified"
4. Click Confirm
Expected: Application status changes to 'verified'
```

**Step 8: Agent Converts Application**
```
1. Click "Convert Application" button
2. Enter notes: "Approved for conversion"
3. Click Confirm
Expected: Application status changes to 'converted'
```

**Step 9: Verify Customer Sees Updates**
```
1. Customer logs in
2. Navigate to /customer/applications
3. Check application status
Expected: Status shows 'converted'
```

---

### Test Case 2: Document Rejection & Resubmission

**Steps 1-5: Same as Happy Path**

**Step 6: Agent Rejects Document**
```
1. On application detail, find "Address Proof"
2. Click "Reject" button
3. Enter reason: "Image quality is poor"
4. Click Confirm
Expected: Document status changes to 'rejected'
         Customer receives notification
```

**Step 7: Customer Resubmits Document**
```
1. Customer logs in
2. Navigate to application
3. Find rejected document "Address Proof"
4. Click "Resubmit Document"
5. Upload new file
6. Click Submit
Expected: New document uploaded
         Old document marked as rejected
         Sent to agent for re-review
```

**Step 8: Agent Reviews & Approves**
```
1. Agent sees new document submission
2. Reviews document
3. Clicks "Approve"
Expected: Document approved
         Application can now proceed
```

---

## API Response Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| 200 | OK | Document approved successfully |
| 201 | Created | Application created |
| 400 | Bad Request | Missing required fields |
| 401 | Unauthorized | Invalid token |
| 403 | Forbidden | Agent cannot access other agent's applications |
| 404 | Not Found | Application not found |
| 422 | Unprocessable Entity | Validation failed |
| 500 | Server Error | Database error |

---

## Authentication Tokens

### Get Customer Token
```
POST /login
Content-Type: application/json

Request:
{
  "email": "customer@example.com",
  "password": "password123"
}

Response (200):
{
  "token": "customer_api_token_here",
  "user": {
    "id": 1,
    "role": "customer"
  }
}

Use in API: Authorization: Bearer {token}
```

### Get Agent Token
```
POST /login
Content-Type: application/json

Request:
{
  "email": "agent@example.com",
  "password": "password123"
}

Response (200):
{
  "token": "agent_api_token_here",
  "user": {
    "id": 1,
    "role": "agent"
  }
}

Use in API: Authorization: Bearer {token}
```

---

## Database Status Progression

### Application Status Flow
```
draft → submitted → verified → converted
  ↓
(if rejected, customer can resubmit)
```

### Document Status Flow
```
uploaded → approved/rejected
              ↓
           (if rejected)
           resubmitted → uploaded → approved
```

---

## Notifications & Events

**Customer Receives Notifications:**
- Application Submitted (to agent)
- Document Approved
- Document Rejected (with reason)
- Application Verified
- Application Converted

**Agent Receives Notifications:**
- New Application Submitted
- Document Resubmitted
- All Documents Approved (ready to verify)

---

## Web URLs Summary

| Action | URL | Method |
|--------|-----|--------|
| Register | `/register` | GET/POST |
| Customer Login | `/login` | GET/POST |
| Customer Dashboard | `/customer/dashboard` | GET |
| New Application | `/customer/applications/create` | GET |
| Submit Application | `/customer/applications` | POST |
| Upload Document | `/customer/applications/{id}/documents` | POST |
| View My Applications | `/customer/applications` | GET |
| Agent Login | `/login` | GET/POST |
| Agent Dashboard | `/agent/dashboard` | GET |
| Agent Applications | `/agent/applications` | GET |
| View Application | `/agent/applications/{id}` | GET |
| Approve Document | `/admin/documents/{id}/approve` | POST |
| Reject Document | `/admin/documents/{id}/reject` | POST |
| Verify Application | `/agent/applications/{id}/verify` | POST |
| Convert Application | `/agent/applications/{id}/convert` | POST |
| Admin - Agents | `/admin/agents` | GET |
| Create Agent | `/admin/agents/create` | GET |
| Store Agent | `/admin/agents` | POST |
| Edit Agent | `/admin/agents/{id}/edit` | GET |
| Update Agent | `/admin/agents/{id}` | PUT |
| Toggle Agent Status | `/admin/agents/{id}/status` | PATCH |

---

## Key Features & Permissions

### Admin Can:
- ✅ Create agents
- ✅ Edit agent details
- ✅ Activate/Deactivate agents
- ✅ View all applications
- ✅ View all customers
- ✅ View reports & analytics

### Agent Can:
- ✅ View assigned applications
- ✅ Review documents
- ✅ Approve/Reject documents
- ✅ Verify applications
- ✅ Convert applications
- ✅ View assigned customers
- ✅ Cannot create other agents

### Customer Can:
- ✅ Create application
- ✅ Upload documents
- ✅ View application status
- ✅ Resubmit rejected documents
- ✅ Cannot access other customer's data
- ✅ Cannot perform agent/admin actions

---

## Testing Tools

### Postman Collection
Use the included `Kanban_Agent_API.postman_collection.json` to test all endpoints.

### Browser Testing
1. Open `/register` for customer signup
2. Open `/login` for all user logins
3. Open respective dashboards based on role

### Database Testing
Check tables:
- `users` - User accounts
- `applications` - Customer applications
- `documents` - Uploaded documents
- `agent_profiles` - Agent details
- `customers` - Customer records
