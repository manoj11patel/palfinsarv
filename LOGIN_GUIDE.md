# Getting Started - Login Access

## ✅ Login System Added

The authentication system is now fully set up and ready to use!

## Quick Start

### 1. Start the Development Server
```bash
cd c:\wamp\www\instant\kanban
php artisan serve
```

The server will start at `http://localhost:8000`

### 2. Access Login Page
```
http://localhost:8000/login
```

### 3. Using Test Credentials

**For Admin Panel Access**, use:
```
Email: admin@digital-system.test
Password: password
```

**For Agent Portal Access**, use:
```
Email: agent1@digital-system.test
Password: password
```

## Available Routes

### Public Routes (No Login Required)
- `/` - Welcome page
- `/login` - Login page (shows if you're not logged in)
- `/register` - User registration page

### Protected Routes (Login Required)
- `/admin/dashboard` - Admin dashboard with KPIs
- `/admin/customers` - Manage customers
- `/admin/applications` - View and manage applications
- `/admin/documents` - Review documents
- `/admin/reports` - View analytics and reports
- `/admin/audit-logs` - View activity logs
- `/admin/users` - Manage system users
- `/logout` - Logout (POST only)

## What You Can Test

### Admin Role (admin@digital-system.test)

✅ **Dashboard**: View system overview with:
- Total applications (12)
- Conversion rate (33.33%)
- Customer count (10)
- Agent count (3)
- Recent activities

✅ **Customers**: 
- Browse 10 test customers
- Create new customers
- Edit customer details
- Assign agents
- Filter by status

✅ **Applications**:
- View 12 test applications in different states
- Filter by status (draft, submitted, verified, converted)
- Verify submitted applications
- Convert verified applications
- View detailed application info

✅ **Documents**:
- Review documents from applications
- Approve documents with notes
- Reject documents with reasons
- Track review status

✅ **Reports**:
- View conversion funnel metrics
- See agent performance rankings
- Check product-wise breakdown
- Filter by date range

✅ **Audit Logs**:
- View all system actions
- Filter by action type or entity
- See who performed what action and when

✅ **Users**:
- Create system users (admin, agent, customer)
- Edit user details
- Change user roles
- Delete users

### Agent Role (agent1@digital-system.test)

❌ Cannot access admin panel (role restricted)
✅ Can use API endpoints with bearer token:

```bash
# Get token
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"agent1@digital-system.test","password":"password"}'

# Use in API requests
curl -H "Authorization: Bearer {token}" http://localhost:8000/api/v1/customers
```

## Test Data Available

### Users (6 Total)
| Email | Password | Role | Access |
|-------|----------|------|--------|
| admin@digital-system.test | password | Admin | ✅ Admin Panel |
| admin2@digital-system.test | password | Admin | ✅ Admin Panel |
| agent1@digital-system.test | password | Agent | ✅ API Only |
| agent2@digital-system.test | password | Agent | ✅ API Only |
| agent3@digital-system.test | password | Agent | ✅ API Only |
| customer@digital-system.test | password | Customer | ✅ Customer Portal |

### Test Applications (12 Total)

**Draft** (1):
- John Draft - Personal Loan (no documents)

**Submitted** (3):
- Sarah Johnson - Personal Loan (2 documents)
- Mike Davis - Home Loan (1 document)
- Emily Wilson - Auto Loan (1 pending doc)

**Verified** (2):
- James Brown - Personal Loan (2 approved docs)
- Sofia Martinez - Home Loan

**Converted** (4):
- Alex Thompson - Personal Loan
- Lucy Chen - Auto Loan
- Robert Anderson - Home Loan
- Diana Foster - Personal Loan

## Features to Try

### Try Workflow
1. **Login** as admin@digital-system.test
2. **Go to Applications** → Filter by "submitted"
3. **Click "View"** on Sarah Johnson's application
4. **Check Documents** → See ID Proof and Salary Slip
5. **Click "Verify Application"** button
6. Notice status changes to "verified"
7. **Click "Convert to Customer"** button
8. Notice status changes to "converted"
9. **Check Reports** → Conversion metrics updated

### Try Document Review
1. **Login** as admin@digital-system.test
2. **Go to Documents** → Filter by "uploaded"
3. **Click "Review"** on any document
4. **Option A**: Click **Approve** with optional notes
5. **Option B**: Click **Reject** with required reason
6. Document status updates immediately
7. View timestamp and reviewer info

### Try Creating a User
1. **Login** as admin@digital-system.test
2. **Go to Users** → Click **Add User**
3. Fill form:
   - Name: Test User
   - Email: test@example.com
   - Password: Password123 (min 8 chars)
   - Role: Agent
4. **Create User** → New agent appears in list
5. **Edit** or **Delete** the user

## API Testing (via Postman)

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication
```
POST /login
{
  "email": "agent1@digital-system.test",
  "password": "password"
}
```

### Common Endpoints
```
GET /customers
GET /applications
GET /documents
GET /reports/conversion-metrics
POST /applications/{id}/submit
```

## Troubleshooting

### Issue: Still getting 404 on login
**Solution**: Services might be stale, try:
```bash
php artisan cache:clear
php artisan config:clear
```

### Issue: Can't login (credentials don't work)
**Solution**: Reseed the database:
```bash
php artisan migrate:fresh --seed
```

### Issue: Changes not showing in browser
**Solution**: Clear browser cache or use Ctrl+Shift+Delete

### Issue: "CSRF token mismatch"
**Solution**: Make sure cookies are enabled and you're using POST requests

## Next Steps

1. **Explore Admin Panel**: Test all features and workflows
2. **Test with Postman**: Use API endpoints for programmatic access
3. **Verify Test Data**: Confirm all 12 applications appear with correct status
4. **Create New Data**: Add customers, applications, documents
5. **Review Reports**: Check conversion metrics and agent performance

## Security Notes

- ✅ Passwords are hashed using bcrypt
- ✅ CSRF protection enabled on all forms
- ✅ Authentication required for all admin routes
- ✅ Role-based access control enforced
- ✅ All actions audited and logged
- ✅ Session expiry configured
- ⚠️ Test credentials should be changed before production

## Session Management

- **Sessions stored in**: `storage/framework/sessions/`
- **Session timeout**: Default Laravel (120 minutes)
- **Remember me**: 1 year cookie if enabled
- **Logout**: Clears session and regenerates token

---

**Status**: ✅ Ready to Use
**Last Updated**: April 17, 2026
**Version**: 1.0
