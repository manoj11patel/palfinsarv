# Messaging API Documentation

## Overview
The messaging system enables agents and admins to communicate with each other. Agents can send messages to administrators, and administrators can reply to messages from agents.

## Base URL
```
/api/v1
```

## Authentication
All endpoints require bearer token authentication using Sanctum.

### Headers
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

---

## Endpoints

### 1. Send a Message

**POST** `/messages/send`

Send a message from the authenticated user (agent or admin) to another user.

#### Request Body
```json
{
    "recipient_id": 2,
    "content": "This is the message content",
    "subject": "Optional subject line",
    "parent_message_id": null
}
```

#### Parameters
- `recipient_id` (required, integer): The ID of the recipient user
- `content` (required, string): The message content (min 1 character)
- `subject` (optional, string): The subject of the message (max 255 characters)
- `parent_message_id` (optional, integer): If replying to an existing message thread

#### Response (201 Created)
```json
{
    "success": true,
    "message": "Message sent successfully.",
    "data": {
        "id": 1,
        "sender_id": 1,
        "recipient_id": 2,
        "content": "This is the message content",
        "subject": "Optional subject line",
        "is_read": false,
        "parent_message_id": null,
        "created_at": "2026-04-19T10:30:00Z",
        "updated_at": "2026-04-19T10:30:00Z",
        "sender": {
            "id": 1,
            "name": "John Agent",
            "email": "john@example.com",
            "role": "agent"
        },
        "recipient": {
            "id": 2,
            "name": "Admin User",
            "email": "admin@example.com",
            "role": "admin"
        }
    }
}
```

#### Validation Rules
- Agents can only send messages to admin
- Admins can only send messages to agents
- Recipient must exist in the database

---

### 2. Get Received Messages

**GET** `/messages/received`

Retrieve messages received by the authenticated user (paged).

#### Query Parameters
- `per_page` (optional, integer, default: 20): Number of messages per page
- `sender_id` (optional, integer): Filter messages by sender ID

#### Example
```
GET /messages/received?per_page=20&sender_id=1
```

#### Response (200 OK)
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "sender_id": 1,
                "recipient_id": 2,
                "content": "This is the message content",
                "subject": "Optional subject",
                "is_read": false,
                "parent_message_id": null,
                "created_at": "2026-04-19T10:30:00Z",
                "updated_at": "2026-04-19T10:30:00Z",
                "sender": {
                    "id": 1,
                    "name": "John Agent",
                    "email": "john@example.com",
                    "role": "agent"
                },
                "recipient": {
                    "id": 2,
                    "name": "Admin User",
                    "email": "admin@example.com",
                    "role": "admin"
                },
                "replies": []
            }
        ],
        "links": {
            "first": "http://localhost/api/v1/messages/received?page=1",
            "last": "http://localhost/api/v1/messages/received?page=1",
            "prev": null,
            "next": null
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "last_page": 1,
            "links": [],
            "path": "http://localhost/api/v1/messages/received",
            "per_page": 20,
            "to": 1,
            "total": 1
        }
    }
}
```

---

### 3. Get Sent Messages

**GET** `/messages/sent`

Retrieve messages sent by the authenticated user (paged).

#### Query Parameters
- `per_page` (optional, integer, default: 20): Number of messages per page
- `recipient_id` (optional, integer): Filter messages by recipient ID

#### Response
Same structure as "Get Received Messages"

---

### 4. Get Conversation Thread

**GET** `/messages/conversation/{userId}`

Get the complete conversation thread between the authenticated user and another user.

#### Path Parameters
- `userId` (required, integer): The ID of the other user in the conversation

#### Response (200 OK)
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "sender_id": 1,
            "recipient_id": 2,
            "content": "First message",
            "subject": null,
            "is_read": true,
            "parent_message_id": null,
            "created_at": "2026-04-19T10:30:00Z",
            "sender": {
                "id": 1,
                "name": "Agent Name",
                "email": "agent@example.com"
            },
            "recipient": {
                "id": 2,
                "name": "Admin Name",
                "email": "admin@example.com"
            }
        },
        {
            "id": 2,
            "sender_id": 2,
            "recipient_id": 1,
            "content": "Reply to first message",
            "subject": null,
            "is_read": true,
            "parent_message_id": 1,
            "created_at": "2026-04-19T11:00:00Z",
            "sender": {
                "id": 2,
                "name": "Admin Name",
                "email": "admin@example.com"
            },
            "recipient": {
                "id": 1,
                "name": "Agent Name",
                "email": "agent@example.com"
            }
        }
    ]
}
```

---

### 5. Mark Message as Read

**POST** `/messages/{message}/read`

Mark a specific message as read.

#### Path Parameters
- `message` (required, integer): The message ID

#### Response (200 OK)
```json
{
    "success": true,
    "message": "Message marked as read."
}
```

#### Error (404 Not Found)
```json
{
    "success": false,
    "message": "Message not found."
}
```

#### Error (403 Forbidden)
```json
{
    "success": false,
    "message": "Unauthorized."
}
```

---

### 6. Get Unread Messages Count

**GET** `/messages/unread/count`

Get the count of unread messages for the authenticated user.

#### Response (200 OK)
```json
{
    "success": true,
    "unread_count": 5
}
```

---

### 7. Get Unread Messages

**GET** `/messages/unread`

Get all unread messages for the authenticated user.

#### Response (200 OK)
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "sender_id": 1,
            "recipient_id": 2,
            "content": "Unread message",
            "subject": "Subject",
            "is_read": false,
            "parent_message_id": null,
            "created_at": "2026-04-19T10:30:00Z",
            "sender": {
                "id": 1,
                "name": "Agent Name",
                "email": "agent@example.com"
            },
            "recipient": {
                "id": 2,
                "name": "Admin Name",
                "email": "admin@example.com"
            }
        }
    ]
}
```

---

### 8. Get All Agents (Admin Only)

**GET** `/agents`

Get a list of all agents in the system (only accessible to admin users).

#### Response (200 OK)
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "John Agent",
            "email": "john@example.com"
        },
        {
            "id": 2,
            "name": "Jane Agent",
            "email": "jane@example.com"
        }
    ]
}
```

#### Error (403 Forbidden)
```json
{
    "success": false,
    "message": "Only admins can access this endpoint."
}
```

---

## Error Handling

All endpoints return appropriate HTTP status codes:

- **200 OK**: Successful GET request
- **201 Created**: Successful resource creation (POST)
- **400 Bad Request**: Validation error
- **403 Forbidden**: Authorization error (insufficient permissions)
- **404 Not Found**: Resource not found
- **500 Internal Server Error**: Server error

### Common Error Response Format
```json
{
    "success": false,
    "message": "Error description"
}
```

---

## Usage Examples

### Agent Sending a Message to Admin

```bash
curl -X POST http://localhost/api/v1/messages/send \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "recipient_id": 2,
    "content": "I need help with a customer application",
    "subject": "Customer Assistance Required"
  }'
```

### Get All Received Messages

```bash
curl -X GET "http://localhost/api/v1/messages/received?per_page=20" \
  -H "Authorization: Bearer {token}"
```

### Get Conversation with Specific User

```bash
curl -X GET http://localhost/api/v1/messages/conversation/1 \
  -H "Authorization: Bearer {token}"
```

### Reply to a Message

```bash
curl -X POST http://localhost/api/v1/messages/send \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "recipient_id": 1,
    "content": "Here is the response to your request",
    "parent_message_id": 1
  }'
```

---

## Admin Panel Features

### Messages Management Page
- **URL**: `/admin/messages`
- **Features**:
  - View all received messages
  - Search messages by subject or content
  - Filter messages by sender (agent)
  - View message count and read/unread status
  - Click to open and reply to messages

### Message Thread View
- **URL**: `/admin/messages/{messageId}`
- **Features**:
  - View complete conversation thread
  - Display sender name and role
  - Display timestamps
  - Reply form at the bottom
  - Back button to message list

### Sidebar Notification
- The admin sidebar displays a badge with the count of unread messages
- Badge disappears when there are no unread messages
- Updates in real-time as messages are received and read

---

## Database Schema

### Messages Table
```
id (bigint, primary key)
sender_id (bigint, foreign key to users)
recipient_id (bigint, foreign key to users)
content (longtext)
subject (string, nullable)
is_read (boolean, default: false)
parent_message_id (bigint, foreign key to messages, nullable)
created_at (timestamp)
updated_at (timestamp)

Indexes:
- (recipient_id, is_read)
- (sender_id, recipient_id)
```

---

## Notes for Mobile App Integration

1. **Agent-Only Actions**: 
   - Agent role can only send messages to users with admin role
   - Admin role can only send messages to users with agent role

2. **Message Threading**: 
   - Parent messages have `parent_message_id = null`
   - Replies have `parent_message_id = parent_message_id`
   - Use the `parent_message_id` to group related messages

3. **Real-time Updates**:
   - For real-time notifications, poll the `/messages/unread/count` endpoint
   - Or implement WebSocket connections for live updates

4. **Pagination**:
   - Messages are paginated with default page size of 20
   - Use `per_page` query parameter to customize

5. **Status Management**:
   - Messages are automatically marked as `is_read = false` when created
   - Call `/messages/{message}/read` endpoint when user opens a message
