# Objective

Build a centralized digital system that:

- Streamlines customer onboarding
- Enables structured document collection
- Tracks application lifecycle in real time
- Provides performance insights for agents and products
- Reduces manual intervention and operational delays

## User Roles

### Admin
- Full access to the system
- Manages agents, customers, and products
- Monitors reports and performance

### Agent
- Logs into the mobile app
- Generates onboarding links
- Manages assigned customers
- Tracks application progress

### Customer
- Accesses onboarding through link or app
- Submits profile details
- Uploads required documents
- Tracks submission status

## Business Workflow

1. Agent logs into the mobile application.
2. Agent generates a unique onboarding link.
3. Agent shares the link with the customer.
4. Customer opens the link and:
   - Enters personal details
   - Uploads documents
   - Saves draft or submits
5. System stores the application and maps it to the agent.
6. Agent reviews the customer submission.
7. Admin monitors overall progress and performance.

## Core Business Features

### 5.1 Customer and Lead Management
- Create and manage customer records
- Prevent duplicate entries
- Track application status

### 5.2 Document Management
- Upload and store documents
- Track document status (pending, verified, rejected)
- Ensure required documents are submitted

### 5.3 Onboarding Link System
- Generate unique onboarding links
- Map links to agents and customers
- Secure access for customers

### 5.4 Reporting and Analytics
- Agent performance reports
- Product-wise reports
- Conversion tracking
- Time-based reporting (daily, monthly, yearly)

### 5.5 Alerts and Reminders
- Pending document alerts
- Expiry reminders
- Follow-up notifications

## Functional Requirements

### Authentication
- Secure login for all users
- Token-based authentication for APIs

### Authorization
- Role-based access control
- Agents can only access assigned data
- Customers can only access their own data

### Customer Lifecycle
- Draft
- Submitted
- Verified
- Converted

### Document Lifecycle
- Uploaded
- Pending Review
- Approved
- Rejected

### Data Validation
- Unique phone and email validation
- Mandatory field enforcement

## Non-Functional Requirements

### Performance
- Fast API response time
- Optimized dashboard loading using caching

### Security
- Secure authentication mechanisms
- Encrypted data handling
- Controlled access to files and APIs

### Scalability
- Support increasing numbers of agents and customers
- Extendable for additional platforms (iOS, web)

### Logging and Auditing
- Track user activities
- Maintain audit logs for critical actions

## Delivery Strategy

### Phase 1: Plan and Scaffold
- [ ] Define an implementation plan with success criteria for each phase.
- [ ] Set up project scaffolding and baseline project files (including `.gitignore`).
- [ ] Define testing standards and unit test expectations.
Success criteria:
- [ ] Project structure is ready for MVP implementation.
- [ ] Phase-level success criteria are documented and testable.

### Phase 2: Execute and Validate
- [ ] Implement according to plan.
- [ ] Verify every phase criterion is satisfied before moving forward.
- [ ] Maintain rigorous unit test coverage for implemented modules.
Success criteria:
- [ ] All planned MVP requirements are implemented.
- [ ] Unit tests pass for core workflows.

### Phase 3: Integrate and Stabilize
- [ ] Perform extensive integration testing with Playwright or similar tooling.
- [ ] Fix defects found during integration testing.
Success criteria:
- [ ] End-to-end onboarding flow is stable and verified.
- [ ] No blocking defects remain for MVP release.

### Phase 4: MVP Readiness
- [ ] Confirm MVP is complete and tested.
- [ ] Ensure server is running and ready for user access.
Success criteria:
- [ ] MVP is deployable and operational.
- [ ] User can access and use the core flow end to end.

## Coding Standards

- Use the latest stable library versions and idiomatic approaches.
- Keep implementation simple: no over-engineering, no unnecessary defensive programming, no extra features beyond scope.
- Keep documentation concise and maintain a minimal README.
- Do not use emojis.