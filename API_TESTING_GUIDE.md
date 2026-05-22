# API Testing Guide - Customer Onboarding Flow

## Quick Reference

This guide provides step-by-step instructions for testing the complete customer onboarding flow using both web interface and API endpoints.

---

## Part 1: Setup & Admin Operations

### 1.1 Admin Creates Agent (Web)

**URL:** `http://localhost/admin/agents/create`  
**Method:** GET/POST  
**Authentication:** Admin login required

**Steps:**
1. Login as admin: `http://localhost/login`
   - Email: admin@example.com
   - Password: admin123

2. Navigate to: `http://localhost/admin/agents`

3. Click "Add Agent" button

4. Fill form:
   ```
   Name: John Sales Agent
   Email: john.agent@company.com
   Employee Code: AGT-2026-001
   Phone: +1-555-0100
   Password: Agent@123
   Confirm Password: Agent@123
   Active: ✓ (checked)
   ```

5. Click "Create Agent"

**Expected Response:**
- Redirect to agents list
- Success message: "Agent created successfully"
- Agent visible in list with "Active" status

**Database Check:**
```sql
SELECT * FROM users WHERE role='agent' AND email='john.agent@company.com';
SELECT * FROM agent_profiles WHERE user_id=<id>;
```

---

### 1.2 Admin Toggles Agent Status (Web)

**URL:** `http://localhost/admin/agents`

**Steps to Deactivate:**
1. In agents list, find agent "John Sales Agent"
2. Click "Deactivate" button in Actions
3. Confirm dialog: "Toggle agent status?"
4. Success message: "Agent deactivated successfully"

**Expected Behavior:**
- Agent status badge changes to "Inactive"
- Agent can no longer login (see 1.3)

**API Endpoint (Alternative):**
```
PATCH /api/v1/admin/agents/{agent_id}/status
Authorization: Bearer {admin_token}
Content-Type: application/json

Request:
{
  "is_active": false
}

Response (200):
{
  "success": true,
  "message": "Agent deactivated successfully",
  "agent": {
    "id": 5,
    "name": "John Sales Agent",
    "is_active": false
  }
}
```

---

## Part 2: Customer Registration & Onboarding Access

### 2.1 Customer Self-Registers (Web)

**URL:** `http://localhost/register`

**Form Fields:**
```
Name: Jane Customer
Email: jane.customer@example.com
Password: Customer@123
Confirm Password: Customer@123
```

**Steps:**
1. Visit `http://localhost/register`
2. Fill registration form
3. Click "Register"

**Expected Response:**
- Redirect to customer dashboard
- Auto-logged in with 'customer' role
- Empty application list

**API Endpoint:**
```
POST /api/v1/auth/register
Content-Type: application/json

Request:
{
  "name": "Jane Customer",
  "email": "jane.customer@example.com",
  "password": "Customer@123",
  "password_confirmation": "Customer@123"
}

Response (201):
{
  "message": "User registered successfully",
  "user": {
    "id": 10,
    "name": "Jane Customer",
    "email": "jane.customer@example.com",
    "role": "customer"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

---

### 2.2 Customer Accesses via Agent Link (Web)

**Prerequisite:** Agent has generated onboarding link

**URL:** `http://localhost/onboarding/{unique_link_code}`  
**Method:** GET  
**Authentication:** None required

**Flow:**
1. Customer receives link from agent (via SMS/Email/WhatsApp)
2. Customer clicks link: `http://localhost/onboarding/LINK_ABC123`
3. System redirects to welcome page

**Welcome Page - Onboarding** (3 options):

**Option A: New Customer (Not Registered)**
1. Click "Create Account" button
2. Redirects to `/register?agent=5`
3. Register with details
4. Redirects to create application with agent pre-selected

**Option B: Existing Customer (Registered)**
1. Already logged in
2. Page shows "Start Application" or "View My Applications"
3. Can create new application
4. Agent is auto-selected from link

**Option C: Logged In But Different Agent**
1. Already logged in
2. Page shows agent info
3. Option to create application or view existing

**API Endpoint:**
```
GET /api/v1/onboarding/{link_code}

Response (200):
{
  "success": true,
  "link_valid": true,
  "agent": {
    "id": 5,
    "name": "John Sales Agent",
    "email": "john.agent@company.com",
    "phone": "+1-555-0100"
  },
  "requires_registration": true,
  "registration_url": "http://localhost/register?agent=5",
  "application_url": "http://localhost/customer/applications/create?agent=5"
}
```

---

## Part 3: Customer Submits Application & Documents

### 3.1 Customer Creates Application (Web)

**URL:** `http://localhost/customer/applications/create`  
**Method:** GET (form) / POST (submit)  
**Authentication:** Customer must be logged in

**Steps:**
1. Customer login: `http://localhost/login`
   - Email: jane.customer@example.com
   - Password: Customer@123

2. Navigate to: `http://localhost/customer/applications/create`

3. **Fill Personal Details Section:**
   ```
   Full Name: Jane Customer
   Email: jane.customer@example.com
   Phone: +1-555-0101
   Date of Birth: 1990-05-15
   Address: 123 Main Street, City, State 12345
   Identification Type: Passport
   Identification Number: US123456789
   ```

4. **Select Product & Agent:**
   ```
   Product: Home Loan
   Agent: John Sales Agent
   ```

5. **Add Documents:**
   - Click "Add Document"
   - Type: Identification Proof → Upload passport.pdf
   - Type: Address Proof → Upload address.pdf

6. **Submit:**
   - Option A: Click "Save as Draft" → Status: draft
   - Option B: Click "Submit Application" → Status: submitted

**Expected Web Response:**
- Redirect to applications list
- Success message: "Application submitted successfully"
- Application shows in list with status badge

---

### 3.2 Customer Submits Application (API)

**Create Application:**
```
POST /api/v1/applications
Authorization: Bearer {customer_token}
Content-Type: application/json

Request:
{
  "full_name": "Jane Customer",
  "email": "jane.customer@example.com",
  "phone": "+1-555-0101",
  "date_of_birth": "1990-05-15",
  "address": "123 Main Street, City, State 12345",
  "identification_type": "passport",
  "identification_number": "US123456789",
  "product_id": 1,
  "agent_user_id": 5,
  "status": "draft"
}

Response (201):
{
  "id": 42,
  "customer_id": 10,
  "agent_id": 5,
  "status": "draft",
  "full_name": "Jane Customer",
  "email": "jane.customer@example.com",
  "documents": [],
  "created_at": "2026-04-22T15:30:00Z"
}
```

**Upload Documents:**
```
POST /api/v1/applications/{application_id}/documents
Authorization: Bearer {customer_token}
Content-Type: multipart/form-data

Request:
- document_type: identification_proof
- file: <binary_file_passport.pdf>
- description: Passport copy

Response (201):
{
  "id": 101,
  "application_id": 42,
  "document_type": "identification_proof",
  "status": "uploaded",
  "file_path": "/storage/documents/app_42_doc_101.pdf",
  "file_url": "http://localhost/storage/documents/app_42_doc_101.pdf",
  "uploaded_at": "2026-04-22T15:35:00Z"
}
```

**Submit Application:**
```
POST /api/v1/applications/{application_id}/submit
Authorization: Bearer {customer_token}
Content-Type: application/json

Request:
{}

Response (200):
{
  "id": 42,
  "status": "submitted",
  "submitted_at": "2026-04-22T15:40:00Z",
  "message": "Application submitted successfully"
}
```

**Database Check:**
```sql
SELECT * FROM applications WHERE id=42;
SELECT * FROM documents WHERE application_id=42;
```

---

## Part 4: Agent Reviews & Documents Verification

### 4.1 Agent Logs In (Web)

**URL:** `http://localhost/login`

**Credentials:**
```
Email: john.agent@company.com
Password: Agent@123
```

**Expected:**
- Redirect to `/agent/dashboard`
- If agent is INACTIVE (is_active=0):
  - Error message: "Your account has been deactivated. Please contact administrator."
  - Redirect back to login

---

### 4.2 Agent Views Applications (Web)

**URL:** `http://localhost/agent/applications`

**List View:**
- Shows all submitted applications assigned to agent
- Displays: Customer name, email, phone, status, document count
- Click on application to view details

**Expected:**
- Application from Jane Customer visible in list
- Status: "submitted"
- Documents: "2" badge

---

### 4.3 Agent Reviews Application Details (Web)

**URL:** `http://localhost/agent/applications/{application_id}`

**View Details:**
1. Customer personal information displayed
2. All uploaded documents listed with status
3. Document preview/download available
4. Action buttons for: Approve, Reject, Verify

**Steps to Review:**
1. Click on Jane's application
2. Review personal details
3. Review each document:
   - Can download/view document
   - Can click "Approve" or "Reject"

---

### 4.4 Agent Approves Documents (Web/API)

**Web - Approve Document:**
1. View application details
2. Find document: "Identification Proof"
3. Click "Approve" button
4. Enter notes: "Document verified successfully"
5. Click confirm

**Expected:**
- Document status changes to "approved"
- Document shows with green "Approved" badge
- Agent name and timestamp recorded

**API - Approve Document:**
```
POST /api/v1/documents/{document_id}/approve
Authorization: Bearer {agent_token}
Content-Type: application/json

Request:
{
  "notes": "Document verified successfully"
}

Response (200):
{
  "id": 101,
  "document_type": "identification_proof",
  "status": "approved",
  "verified_at": "2026-04-22T16:00:00Z",
  "verified_by": "John Sales Agent"
}
```

**Repeat for all documents (Address Proof, etc.)**

---

### 4.5 Agent Rejects Document (Web/API)

**Web - Reject Document:**
1. View application details
2. Find document: "Address Proof"
3. Click "Reject" button
4. Enter reason: "Image quality is poor. Please upload clearer photo."
5. Click confirm

**Expected:**
- Document status changes to "rejected"
- Document shows with red "Rejected" badge
- Rejection reason displayed
- Customer receives notification

**API - Reject Document:**
```
POST /api/v1/documents/{document_id}/reject
Authorization: Bearer {agent_token}
Content-Type: application/json

Request:
{
  "rejection_reason": "Image quality is poor. Please upload clearer photo."
}

Response (200):
{
  "id": 102,
  "document_type": "address_proof",
  "status": "rejected",
  "rejection_reason": "Image quality is poor. Please upload clearer photo.",
  "rejected_at": "2026-04-22T16:05:00Z"
}
```

**Database Check:**
```sql
SELECT * FROM documents WHERE application_id=42;
-- Should show: identification_proof=approved, address_proof=rejected
```

---

## Part 5: Customer Resubmits Rejected Document

### 5.1 Customer Views Application (Web)

**URL:** `http://localhost/customer/applications/{application_id}`

**Expected:**
- Personal details visible
- Documents section shows:
  - ✓ Identification Proof (Approved - green badge)
  - ✗ Address Proof (Rejected - red badge)
  - Rejection reason: "Image quality is poor..."

---

### 5.2 Customer Resubmits Document (Web)

**Steps:**
1. Customer logs in
2. Navigate to `/customer/applications/{application_id}`
3. Find rejected document section
4. Click "Resubmit Document" button
5. Upload new file: address_clear.pdf
6. Click "Resubmit"

**Expected:**
- New document created with status "uploaded"
- Old document marked as "superseded"
- Notification to agent
- Agent can review again

**Web Form:**
```html
<form method="POST" action="/customer/applications/{app_id}/documents/{doc_id}/resubmit">
  <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required>
  <textarea name="notes">Uploading clearer copy</textarea>
  <button>Resubmit</button>
</form>
```

---

### 5.3 Customer Resubmits Document (API)

```
POST /api/v1/applications/{application_id}/documents/{document_id}/resubmit
Authorization: Bearer {customer_token}
Content-Type: multipart/form-data

Request:
- file: <binary_file_address_clear.pdf>
- notes: Uploading clearer copy

Response (201):
{
  "id": 103,
  "application_id": 42,
  "document_type": "address_proof",
  "status": "uploaded",
  "previous_document_id": 102,
  "previous_status": "rejected",
  "uploaded_at": "2026-04-22T16:30:00Z"
}
```

---

### 5.4 Agent Approves Resubmitted Document (Web/API)

**Same as 4.4 - Approve Document**

```
POST /api/v1/documents/{document_id}/approve
Authorization: Bearer {agent_token}

Request:
{
  "notes": "Clear copy received and verified"
}

Response (200):
{
  "id": 103,
  "status": "approved",
  "verified_at": "2026-04-22T16:35:00Z"
}
```

**Expected:**
- All documents now approved
- Agent can now verify entire application

---

## Part 6: Agent Verifies Application

### 6.1 Agent Verifies Application (Web)

**URL:** `http://localhost/agent/applications/{application_id}`

**Prerequisite:** All documents must be "approved"

**Steps:**
1. View application details
2. Ensure all documents are approved (green badges)
3. Click "Verify Application" button
4. Enter notes: "All documents verified. Customer eligible for next step."
5. Click confirm

**Expected:**
- Application status changes to "verified"
- Status badge changes to green "Verified"
- Timestamp recorded
- Customer receives notification

---

### 6.2 Agent Verifies Application (API)

```
POST /api/v1/applications/{application_id}/verify
Authorization: Bearer {agent_token}
Content-Type: application/json

Request:
{
  "notes": "All documents verified and customer is eligible"
}

Response (200):
{
  "id": 42,
  "status": "verified",
  "verified_at": "2026-04-22T16:40:00Z",
  "verified_by_agent": "John Sales Agent",
  "message": "Application verified successfully"
}
```

**Database Check:**
```sql
SELECT * FROM applications WHERE id=42;
-- Should show: status='verified', verified_at=timestamp
```

---

## Part 7: Agent Converts Application

### 7.1 Agent Converts Application (Web)

**URL:** `http://localhost/agent/applications/{application_id}`

**Prerequisite:** Application must be "verified"

**Steps:**
1. View verified application
2. Click "Convert Application" button
3. Enter notes: "Customer approved for conversion. Effective today."
4. Choose effective date: 2026-04-22
5. Click confirm

**Expected:**
- Application status changes to "converted"
- Status badge changes to blue "Converted"
- Conversion timestamp recorded
- Customer receives confirmation notification

---

### 7.2 Agent Converts Application (API)

```
POST /api/v1/applications/{application_id}/convert
Authorization: Bearer {agent_token}
Content-Type: application/json

Request:
{
  "notes": "Customer approved for conversion",
  "effective_date": "2026-04-22"
}

Response (200):
{
  "id": 42,
  "status": "converted",
  "converted_at": "2026-04-22T16:45:00Z",
  "converted_by_agent": "John Sales Agent",
  "effective_date": "2026-04-22",
  "message": "Application converted successfully"
}
```

**Database Check:**
```sql
SELECT * FROM applications WHERE id=42;
-- Should show: status='converted', converted_at=timestamp
```

---

## Part 8: Customer Verifies Final Status

### 8.1 Customer Views Final Status (Web)

**URL:** `http://localhost/customer/applications`

**Expected:**
- Application shows with status "Converted" (blue badge)
- Timeline shows all steps:
  - ✓ Created
  - ✓ Submitted
  - ✓ Verified
  - ✓ Converted

**Application Details View:**
- All documents approved
- Personal details visible
- Agent information visible
- Complete timeline

---

### 8.2 Get Application Details (API)

```
GET /api/v1/customer/applications/{application_id}
Authorization: Bearer {customer_token}

Response (200):
{
  "id": 42,
  "status": "converted",
  "customer": {
    "id": 10,
    "full_name": "Jane Customer",
    "email": "jane.customer@example.com",
    "phone": "+1-555-0101"
  },
  "agent": {
    "id": 5,
    "name": "John Sales Agent"
  },
  "product": {
    "id": 1,
    "name": "Home Loan"
  },
  "documents": [
    {
      "id": 101,
      "type": "identification_proof",
      "status": "approved"
    },
    {
      "id": 103,
      "type": "address_proof",
      "status": "approved"
    }
  ],
  "created_at": "2026-04-22T15:30:00Z",
  "submitted_at": "2026-04-22T15:40:00Z",
  "verified_at": "2026-04-22T16:40:00Z",
  "converted_at": "2026-04-22T16:45:00Z"
}
```

---

## Testing Checklist

### Admin Operations
- [ ] Create agent successfully
- [ ] View agent list
- [ ] Edit agent details
- [ ] Activate/Deactivate agent
- [ ] Deactivated agent cannot login

### Customer Registration & Access
- [ ] Self-register via `/register`
- [ ] Access via agent onboarding link
- [ ] Onboarding page shows correct agent info
- [ ] Can create application from onboarding link

### Application Submission
- [ ] Create application with all fields
- [ ] Save as draft
- [ ] Submit application (status changes to submitted)
- [ ] Upload documents
- [ ] Multiple documents can be added
- [ ] Application appears in customer dashboard
- [ ] Application appears in agent list

### Agent Review Process
- [ ] Agent sees application in list
- [ ] Can view all application details
- [ ] Can download documents
- [ ] Can approve documents
- [ ] Can reject documents with reason
- [ ] Rejection reason sent to customer

### Document Resubmission
- [ ] Customer sees rejected document
- [ ] Rejection reason visible
- [ ] Resubmit button available
- [ ] Can upload new document
- [ ] New document has "uploaded" status
- [ ] Agent can approve new document

### Application Verification
- [ ] Agent cannot verify if documents not approved
- [ ] Can verify when all approved
- [ ] Status changes to "verified"
- [ ] Timeline updated

### Application Conversion
- [ ] Agent cannot convert if not verified
- [ ] Can convert when verified
- [ ] Status changes to "converted"
- [ ] Customer sees final status
- [ ] Timeline shows all stages

---

## Common API Test Patterns

### Get Customer Token
```
POST /api/v1/auth/login
{
  "email": "jane.customer@example.com",
  "password": "Customer@123"
}
```

### Get Agent Token
```
POST /api/v1/auth/login
{
  "email": "john.agent@company.com",
  "password": "Agent@123"
}
```

### Test Invalid Token
```
GET /api/v1/applications
Authorization: Bearer invalid_token_here

Response (401):
{
  "message": "Unauthenticated"
}
```

### Test Forbidden Access
```
GET /api/v1/applications/{other_customer_app_id}
Authorization: Bearer {customer_token}

Response (403):
{
  "message": "You are not authorized to access this application"
}
```

---

## Debugging Tips

### Check Agent Status in DB
```sql
SELECT u.id, u.name, u.email, ap.is_active 
FROM users u 
JOIN agent_profiles ap ON u.id = ap.user_id 
WHERE u.role = 'agent';
```

### Check Application Flow
```sql
SELECT id, status, submitted_at, verified_at, converted_at 
FROM applications 
WHERE id = 42;
```

### Check Document Status
```sql
SELECT document_type, status, rejection_reason 
FROM documents 
WHERE application_id = 42;
```

### Check Login Logs
```sql
SELECT * FROM audit_logs 
WHERE user_id = 5 AND action = 'login' 
ORDER BY created_at DESC LIMIT 10;
```

---

## Performance Testing

### Bulk Application Load
```
Create 100 applications for same agent:
- Use loop in API testing tool
- Measure agent list page load time
- Check pagination performance

Expected: < 2 seconds to load page
```

### Document Upload Stress
```
Upload 50 documents to single application:
- Test file size limits
- Test storage handling
- Measure upload time

Expected: Each upload < 5 seconds
```

---

## Security Testing

### Test Access Control
- [ ] Customer cannot access other customer's applications
- [ ] Agent cannot verify other agent's documents
- [ ] Customer cannot approve documents
- [ ] Deactivated agent cannot login

### Test Data Validation
- [ ] Reject duplicate employee codes
- [ ] Validate date of birth format
- [ ] Validate phone number format
- [ ] Validate file upload types

### Test Token Security
- [ ] Expired tokens rejected
- [ ] Invalid tokens rejected
- [ ] Tokens cannot be reused after logout
