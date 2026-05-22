# Messaging Feature Implementation Summary

## Overview
A complete messaging system has been implemented allowing agents and admins to communicate bidirectionally through APIs and an admin web panel.

## Changes Made

### 1. Database
- **Migration**: `database/migrations/2026_04_19_000000_create_messages_table.php`
  - Creates `messages` table with columns for sender, recipient, content, subject, read status, and parent message reference
  - Includes indexes on `(recipient_id, is_read)` and `(sender_id, recipient_id)` for performance
  - Supports message threading via `parent_message_id` foreign key

### 2. Models
- **New Model**: `app/Models/Message.php`
  - Relations: `sender()`, `recipient()`, `parentMessage()`, `replies()`
  - Scopes: `unread()`, `betweenUsers()`
  - Fillable attributes: sender_id, recipient_id, content, subject, is_read, parent_message_id

- **Updated Model**: `app/Models/User.php`
  - Added relations: `sentMessages()`, `receivedMessages()`

### 3. API Controllers & Routes
- **New Controller**: `app/Http/Controllers/Api/MessageController.php`
  - `send()` - Send messages (validates role restrictions)
  - `getReceivedMessages()` - Paginated received messages
  - `getSentMessages()` - Paginated sent messages
  - `getConversation()` - Get full conversation thread
  - `markAsRead()` - Mark message as read
  - `getUnreadCount()` - Count unread messages
  - `getUnreadMessages()` - Get all unread messages
  - `getAgents()` - Get list of agents (admin only)

- **Updated Routes**: `routes/api.php`
  - Added 7 new endpoints under authenticated, role-based middleware
  - All endpoints accessible via `/api/v1/messages/*` and `/api/v1/agents`

### 4. Web Admin Panel
- **New Controller**: `app/Http/Controllers/Web/MessageManagementController.php`
  - `index()` - List messages with search & filter
  - `show()` - Display message thread
  - `reply()` - Send reply to a message
  - `getUnreadCount()` - Helper for unread notification

- **New Views**:
  - `resources/views/admin/messages/index.blade.php` - Message list page
  - `resources/views/admin/messages/show.blade.php` - Message thread & reply form

- **Updated Routes**: `routes/web.php`
  - Added 3 new routes under admin middleware
  - Routes: `/admin/messages`, `/admin/messages/{id}`, POST `/admin/messages/{id}/reply`

- **Updated Layout**: `resources/views/layouts/admin.blade.php`
  - Added "Messages" link in sidebar
  - Added unread message badge notification (updates dynamically)

### 5. Documentation
- **API Docs**: `docs/messaging-api.md`
  - Comprehensive endpoint documentation
  - Request/response examples
  - Error handling information
  - Usage examples for mobile app integration
  - Database schema reference

## Features

### Agent Features
✅ Send messages to admin  
✅ View received messages from admin  
✅ View conversation threads  
✅ View unread message count  
✅ Mark messages as read  

### Admin Features  
✅ Receive messages from agents  
✅ View all received messages with pagination  
✅ Search messages by content/subject  
✅ Filter messages by sender  
✅ View message threads  
✅ Reply to agent messages  
✅ Unread message notification in sidebar  

### API Features
✅ Role-based access control (agents ↔ admin only)  
✅ Message threading support  
✅ Pagination support  
✅ Read/unread status tracking  
✅ Search and filtering  
✅ Timestamps on all messages  

## API Endpoints Summary

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/v1/messages/send` | Send message | Required |
| GET | `/api/v1/messages/received` | Get received messages | Required |
| GET | `/api/v1/messages/sent` | Get sent messages | Required |
| GET | `/api/v1/messages/conversation/{userId}` | Get conversation thread | Required |
| POST | `/api/v1/messages/{id}/read` | Mark as read | Required |
| GET | `/api/v1/messages/unread/count` | Unread count | Required |
| GET | `/api/v1/messages/unread` | Get unread messages | Required |
| GET | `/api/v1/agents` | Get agents list | Admin only |

## Web Routes Summary

| Method | Route | Description | Auth |
|--------|-------|-------------|------|
| GET | `/admin/messages` | Message list | Admin |
| GET | `/admin/messages/{id}` | View thread | Admin |
| POST | `/admin/messages/{id}/reply` | Send reply | Admin |

## Running the Migration

To apply the database migration:

```bash
php artisan migrate
```

This will create the `messages` table with all required columns and indexes.

## Testing the APIs

### 1. Agent Sends Message to Admin

```bash
curl -X POST http://localhost/api/v1/messages/send \
  -H "Authorization: Bearer {agent_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "recipient_id": 2,
    "content": "Need help with customer XYZ",
    "subject": "Customer Assistance"
  }'
```

### 2. Admin Views Received Messages

```bash
curl -X GET "http://localhost/api/v1/messages/received?per_page=20" \
  -H "Authorization: Bearer {admin_token}"
```

### 3. Admin Replies to Message

```bash
curl -X POST http://localhost/api/v1/messages/send \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "recipient_id": 1,
    "content": "Here is the solution...",
    "parent_message_id": 1
  }'
```

### 4. Agent Views Conversation

```bash
curl -X GET http://localhost/api/v1/messages/conversation/2 \
  -H "Authorization: Bearer {agent_token}"
```

## Admin Panel Access

1. Login to admin panel
2. In sidebar, click "Messages" (shows unread count in red badge)
3. View list of all messages from agents
4. Click "View & Reply" to open conversation
5. Type reply and submit

## Notes for Mobile App Development

1. **Polling**: Implement polling on `/messages/unread/count` endpoint to check for new messages
2. **Threading**: Group messages using `parent_message_id` to display conversations
3. **Pagination**: Handle pagination using `per_page` parameter (default 20)
4. **Read Status**: Call `/messages/{id}/read` when user opens a message
5. **Error Handling**: Agents cannot send to agents, admins cannot send to admins (403 error)
6. **Timestamps**: All timestamps are in ISO 8601 format (UTC)

## Future Enhancements

Potential features for future phases:
- Real-time WebSocket notifications
- Message attachments/file uploads
- Message editing/deletion
- Message archiving/tagging
- Bulk message operations
- Message analytics/reporting
