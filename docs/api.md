# Digital System API (MVP)

Base URL: `/api/v1`

## Authentication

### POST `/login`
- Request: `email`, `password`
- Response: bearer token and user object

### POST `/logout` (auth required)
- Invalidates current token.

## Agent/Admin Protected Endpoints

### Customers
- `GET /customers` - list customers (`admin`: all, `agent`: assigned only)
- `POST /customers` - create customer for current agent

### Applications
- `GET /applications` - list applications (`admin`: all, `agent`: assigned only)
- `POST /applications` - create draft application
- `POST /applications/{application}/submit` - transition `draft -> submitted`

### Documents
- `GET /documents` - list documents with role-based scope
- `POST /documents` - upload document file for application
  - Multipart fields: `application_id`, `document_type`, `file`

### Onboarding Links
- `POST /onboarding-links` - generate onboarding link token

### Reports
- `GET /reports/agent-summary` - application count grouped by status per agent
- `GET /reports/product-wise` - applications grouped by product and status
  - Query params: `start_date` (optional), `end_date` (optional)
- `GET /reports/conversion-metrics` - overall conversion metrics
  - Query params: `start_date` (optional), `end_date` (optional)
  - Response: total, submitted, verified, converted counts and conversion rates
- `GET /reports/agent-performance` - agent performance KPIs
  - Query params: `start_date` (optional), `end_date` (optional)
  - Returns: agent_id, name, total applications, converted count, conversion rate (admin only, sorted by conversion rate)

## Admin Only

- `POST /documents/{document}/review` with `status` (`approved` or `rejected`) and optional `review_note`
- `POST /applications/{application}/verify` - transition `submitted -> verified`
- `POST /applications/{application}/convert` - transition `verified -> converted`

## Public Onboarding

### GET `/onboarding/{token}`
- Validates token and returns token metadata.

### POST `/onboarding/{token}/submit`
- Submits onboarding without authentication.
- Creates/updates customer, creates submitted application, and marks token used.
- Token is single-use (re-submit returns `409`).
- Accepts:
  - `full_name`, `phone`, `email`, `product_id`
  - optional `profile_payload` object
  - optional `documents[]` multipart entries:
    - `documents[n][document_type]`
    - `documents[n][file]`

## Seeded Demo Users

- Admin: `admin@digital-system.test` / `password`
- Agent: `agent@digital-system.test` / `password`
- Customer: `customer@digital-system.test` / `password`

## Audit Logging

The system automatically logs all critical actions:

- **Application Created**: Log when agent creates new application
- **Application Submitted**: Log draft -> submitted transition
- **Application Verified**: Log submitted -> verified transition (admin only)
- **Application Converted**: Log verified -> converted transition (admin only)
- **Document Uploaded**: Log file uploads with document type
- **Document Reviewed**: Log document review with approval/rejection status

All audit logs are stored in `audit_logs` table with:
- `user_id` - user who performed the action
- `action` - type of action performed
- `entity_type` - model type (Application, Document)
- `entity_id` - ID of the affected entity
- `meta` - JSON object with additional context
- `created_at`, `updated_at` - timestamps

## Notifications System

Notification infrastructure is available (`notifications` table) for future integration of:
- Email alerts for pending documents
- Expiry reminders for onboarding links
- Follow-up notifications for overdue applications
- Status change notifications to customers

Notifications can be marked as:
- `sent_at` - timestamp when notification was delivered
- `read_at` - timestamp when user read the notification
