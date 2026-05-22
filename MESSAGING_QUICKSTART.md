# Messaging Feature - Quick Start Guide

## What's New?

A complete bidirectional messaging system has been added to enable:
- ✅ Agents to send messages to admins via mobile app
- ✅ Admins to receive messages and reply via web admin panel
- ✅ Agents to receive admin replies via API in mobile app
- ✅ Message threads with full conversation history
- ✅ Unread message notifications in admin sidebar
- ✅ Search and filter capabilities

---

## Installation Steps

### Step 1: Run Database Migration

```bash
php artisan migrate
```

This creates the `messages` table with proper indexes and foreign keys.

### Step 2: Verify Files

Ensure these new files are present:

**Models**:
- `app/Models/Message.php` ✓
- `app/Models/User.php` (updated) ✓

**Controllers**:
- `app/Http/Controllers/Api/MessageController.php` ✓
- `app/Http/Controllers/Web/MessageManagementController.php` ✓

**Views**:
- `resources/views/admin/messages/index.blade.php` ✓
- `resources/views/admin/messages/show.blade.php` ✓

**Routes**:
- `routes/api.php` (updated) ✓
- `routes/web.php` (updated) ✓

**Layouts**:
- `resources/views/layouts/admin.blade.php` (updated) ✓

**Documentation**:
- `docs/messaging-api.md` ✓
- `docs/messaging-implementation.md` ✓
- `docs/mobile-messaging-guide.md` ✓

---

## Usage

### For Admin Users

1. **Login to Admin Panel**
   - Go to `http://your-server.com/admin/dashboard`
   - Use admin credentials

2. **Check Messages**
   - Click "Messages" in the sidebar
   - Unread message count appears in red badge
   - View all messages from agents

3. **Reply to Messages**
   - Click "View & Reply" on any message
   - See the full conversation thread
   - Type your reply at the bottom
   - Submit to send

4. **Search Messages**
   - Use the search box to find by subject or content
   - Filter by agent name using the dropdown

### For Mobile App (Agent)

1. **Send Message to Admin**
   ```
   POST /api/v1/messages/send
   {
     "recipient_id": 2,
     "content": "Your message",
     "subject": "Optional subject"
   }
   ```

2. **Check for New Messages**
   ```
   GET /api/v1/messages/unread/count
   ```

3. **Get All Messages**
   ```
   GET /api/v1/messages/received
   ```

4. **View Conversation**
   ```
   GET /api/v1/messages/conversation/2
   ```

5. **Reply to Message**
   ```
   POST /api/v1/messages/send
   {
     "recipient_id": 2,
     "content": "Your reply",
     "parent_message_id": 1
   }
   ```

---

## API Endpoints Reference

### Messages
- `POST /api/v1/messages/send` - Send a message
- `GET /api/v1/messages/received` - Get received messages
- `GET /api/v1/messages/sent` - Get sent messages
- `GET /api/v1/messages/conversation/{userId}` - Get conversation thread
- `POST /api/v1/messages/{id}/read` - Mark as read
- `GET /api/v1/messages/unread/count` - Count unread
- `GET /api/v1/messages/unread` - Get unread messages

### Agents List
- `GET /api/v1/agents` - Get all agents (admin only)

---

## Admin Panel Routes

- `GET /admin/messages` - View all messages
- `GET /admin/messages/{id}` - View message thread
- `POST /admin/messages/{id}/reply` - Send reply

---

## Testing

### Quick Test with cURL

**1. Login and get token**
```bash
curl -X POST http://localhost/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "agent@example.com",
    "password": "password"
  }'
```

**2. Send message**
```bash
curl -X POST http://localhost/api/v1/messages/send \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "recipient_id": 1,
    "content": "Test message",
    "subject": "Test"
  }'
```

**3. Get messages**
```bash
curl -X GET http://localhost/api/v1/messages/received \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## File Structure

```
kanban/
├── app/
│   ├── Models/
│   │   ├── Message.php (NEW)
│   │   └── User.php (UPDATED)
│   └── Http/Controllers/
│       ├── Api/
│       │   └── MessageController.php (NEW)
│       └── Web/
│           └── MessageManagementController.php (NEW)
├── database/
│   └── migrations/
│       └── 2026_04_19_000000_create_messages_table.php (NEW)
├── resources/views/
│   ├── admin/
│   │   └── messages/ (NEW)
│   │       ├── index.blade.php
│   │       └── show.blade.php
│   └── layouts/
│       └── admin.blade.php (UPDATED)
├── routes/
│   ├── api.php (UPDATED)
│   └── web.php (UPDATED)
└── docs/
    ├── messaging-api.md (NEW)
    ├── messaging-implementation.md (NEW)
    └── mobile-messaging-guide.md (NEW)
```

---

## Database Schema

### Messages Table

```
Column             Type        Notes
─────────────────────────────────────────────
id                 bigint      Primary key
sender_id          bigint      Foreign key to users
recipient_id       bigint      Foreign key to users
content            longtext    Message body
subject            string      Optional subject
is_read            boolean     Default: false
parent_message_id  bigint      Foreign key to messages (nullable)
created_at         timestamp   Auto-generated
updated_at         timestamp   Auto-generated

Indexes:
- (recipient_id, is_read) - For quick unread queries
- (sender_id, recipient_id) - For conversation queries
```

---

## Key Features

✅ **Role-Based Access**
- Agents can only send to admins
- Admins can only receive from agents

✅ **Message Threading**
- Parent messages have `parent_message_id = NULL`
- Replies have `parent_message_id = msg_id`

✅ **Pagination Support**
- Default 20 messages per page
- Customizable via `per_page` query param

✅ **Read/Unread Tracking**
- Auto-marked as unread on creation
- Admin sidebar shows badge count
- Can be marked as read via API

✅ **Search & Filter**
- Search by subject and content
- Filter by sender name
- Pagination navigation

---

## Troubleshooting

**Q: Migration not running?**
- Ensure database is running
- Check database credentials in `.env`
- Run `php artisan migrate --force` if prompted

**Q: Admin sidebar not showing message badge?**
- Ensure admin layout was updated
- Clear browser cache
- Check for syntax errors in layout file

**Q: API returning 403 Forbidden?**
- Verify user role is correct (admin or agent)
- For agents: ensure recipient is admin
- For admins: ensure recipient is agent

**Q: No unread messages showing?**
- Database migration may not have run
- Check `is_read` status in messages table
- Verify recipient_id matches auth user

---

## Next Steps

1. **Run migrations**: `php artisan migrate`
2. **Test admin panel**: Login and go to `/admin/messages`
3. **Test API**: Use curl or Postman to test endpoints
4. **Integrate mobile app**: Follow `/docs/mobile-messaging-guide.md`
5. **Deploy**: Push changes to production

---

## Documentation

For detailed information, see:
- **API Docs**: `docs/messaging-api.md` - Full endpoint reference
- **Implementation**: `docs/messaging-implementation.md` - Technical details
- **Mobile Guide**: `docs/mobile-messaging-guide.md` - Mobile app integration

---

## Support & Questions

For questions or issues:
1. Check the documentation files
2. Review error messages in logs
3. Verify file syntax and permissions
4. Test with provided curl examples

---

**Last Updated**: April 19, 2026
**Version**: 1.0
**Status**: Ready for deployment
