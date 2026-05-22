# Mobile App - Messaging Integration Guide

## Quick Reference for Agent Mobile App

### Base URL
```
http://your-server.com/api/v1
```

### Authentication
All requests require the authorization token received after login:
```
Authorization: Bearer {access_token}
```

---

## Common Flows

### 1. Send Message to Admin

```javascript
POST /messages/send
Content-Type: application/json

{
  "recipient_id": 2,  // Admin user ID
  "content": "Message text",
  "subject": "Optional subject"
}
```

**Success Response (201)**:
```json
{
  "success": true,
  "message": "Message sent successfully.",
  "data": {
    "id": 123,
    "sender_id": 1,
    "recipient_id": 2,
    "content": "Message text",
    "subject": "Optional subject",
    "is_read": false,
    "created_at": "2026-04-19T10:30:00Z"
  }
}
```

---

### 2. Poll for Unread Messages

```javascript
GET /messages/unread/count
```

**Response (200)**:
```json
{
  "success": true,
  "unread_count": 3
}
```

**Implementation Tip**: Call this every 30-60 seconds to check for new messages.

---

### 3. Fetch All Received Messages

```javascript
GET /messages/received?per_page=20&page=1
```

**Response (200)**:
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "sender_id": 2,
        "recipient_id": 1,
        "content": "Response from admin",
        "subject": "Re: Your request",
        "is_read": false,
        "parent_message_id": null,
        "created_at": "2026-04-19T11:00:00Z",
        "sender": {
          "id": 2,
          "name": "Admin Name",
          "email": "admin@example.com"
        }
      }
    ],
    "meta": {
      "current_page": 1,
      "total": 10,
      "per_page": 20,
      "last_page": 1
    }
  }
}
```

---

### 4. View Conversation Thread

```javascript
GET /messages/conversation/2
```

Returns all messages between you and user ID 2 (admin), ordered chronologically.

**Response (200)**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "sender_id": 1,
      "recipient_id": 2,
      "content": "Initial message",
      "parent_message_id": null,
      "created_at": "2026-04-19T10:00:00Z",
      "sender": {"id": 1, "name": "Me", "email": "me@example.com"}
    },
    {
      "id": 2,
      "sender_id": 2,
      "recipient_id": 1,
      "content": "Admin reply",
      "parent_message_id": 1,
      "created_at": "2026-04-19T11:00:00Z",
      "sender": {"id": 2, "name": "Admin", "email": "admin@example.com"}
    }
  ]
}
```

---

### 5. Mark Message as Read

```javascript
POST /messages/{message_id}/read
```

Example:
```javascript
POST /messages/5/read
```

**Response (200)**:
```json
{
  "success": true,
  "message": "Message marked as read."
}
```

---

### 6. Reply to Message

To reply to a conversation thread, send a new message with the parent message ID:

```javascript
POST /messages/send
Content-Type: application/json

{
  "recipient_id": 2,
  "content": "My reply to your message",
  "parent_message_id": 1  // ID of the original message
}
```

---

## UI Implementation Recommendations

### Messages List Screen
```
┌─────────────────────────────┐
│ Messages                    │
├─────────────────────────────┤
│ ┌─────────────────────────┐ │
│ │ Admin Name         [3] │ │ ← Unread count badge
│ │ "Your latest reply..." │ │
│ │ 2 mins ago              │ │
│ └─────────────────────────┘ │
│ ┌─────────────────────────┐ │
│ │ Admin Name         [0] │ │
│ │ "Thanks for the info"  │ │
│ │ 1 hour ago              │ │
│ └─────────────────────────┘ │
└─────────────────────────────┘
```

### Message Thread Screen
```
┌──────────────────────────────┐
│ < Admin Name                 │
├──────────────────────────────┤
│ [Your Message]               │
│ "Can you help with..."       │
│ 10:00 AM                     │
│                              │
│              [Admin Message] │
│              "Sure, here's..." │
│              10:30 AM        │
├──────────────────────────────┤
│ Write your reply...          │
│                              │
│ [Send]                       │
└──────────────────────────────┘
```

---

## Error Handling

### Common Error Responses

**400 Bad Request** - Validation failed:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "content": ["The content field is required."]
  }
}
```

**403 Forbidden** - Permission denied:
```json
{
  "success": false,
  "message": "Agents can only send messages to admin."
}
```

**404 Not Found** - Resource not found:
```json
{
  "success": false,
  "message": "Message not found."
}
```

---

## Implementation Checklist

- [ ] Add login and get access token
- [ ] Create messages list screen
- [ ] Implement unread message polling (every 30-60 seconds)
- [ ] Create send message screen/modal
- [ ] Add conversation view with message thread
- [ ] Implement mark as read on message open
- [ ] Add reply functionality
- [ ] Display sender name and timestamp
- [ ] Show read/unread status indicator
- [ ] Handle errors with user-friendly messages
- [ ] Add loading states during API calls
- [ ] Refresh message list on pull-to-refresh

---

## Code Examples

### React Native Example

```javascript
import axios from 'axios';

const API_BASE_URL = 'http://your-server.com/api/v1';

const sendMessage = async (token, recipientId, content, subject = null) => {
  try {
    const response = await axios.post(
      `${API_BASE_URL}/messages/send`,
      {
        recipient_id: recipientId,
        content: content,
        subject: subject
      },
      {
        headers: {
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json'
        }
      }
    );
    return response.data;
  } catch (error) {
    console.error('Error sending message:', error);
    throw error;
  }
};

const getReceivedMessages = async (token, page = 1, perPage = 20) => {
  try {
    const response = await axios.get(
      `${API_BASE_URL}/messages/received?page=${page}&per_page=${perPage}`,
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    );
    return response.data;
  } catch (error) {
    console.error('Error fetching messages:', error);
    throw error;
  }
};

const markAsRead = async (token, messageId) => {
  try {
    const response = await axios.post(
      `${API_BASE_URL}/messages/${messageId}/read`,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    );
    return response.data;
  } catch (error) {
    console.error('Error marking message as read:', error);
    throw error;
  }
};

const pollUnreadCount = (token, onUnreadChange) => {
  const interval = setInterval(async () => {
    try {
      const response = await axios.get(
        `${API_BASE_URL}/messages/unread/count`,
        {
          headers: {
            Authorization: `Bearer ${token}`
          }
        }
      );
      onUnreadChange(response.data.unread_count);
    } catch (error) {
      console.error('Error polling unread count:', error);
    }
  }, 30000); // Poll every 30 seconds
  
  return interval;
};
```

### Usage:

```javascript
// Send message
await sendMessage(token, 2, "Hey admin, need your help!");

// Poll for updates
const pollInterval = pollUnreadCount(token, (count) => {
  console.log(`You have ${count} unread messages`);
});

// Stop polling when component unmounts
clearInterval(pollInterval);

// Get messages
const messages = await getReceivedMessages(token);

// Mark as read
await markAsRead(token, 5);
```

---

## Performance Tips

1. **Pagination**: Don't fetch all messages at once. Use pagination with `per_page=20`
2. **Polling**: Poll for unread count every 30-60 seconds, not every second
3. **Caching**: Cache message list locally and only refresh on send/receive
4. **Threading**: Group messages by `parent_message_id` instead of separate API calls
5. **Lazy Loading**: Load older messages on scroll

---

## Support

For issues or questions about the messaging API, refer to:
- API Documentation: `/docs/messaging-api.md`
- Implementation Guide: `/docs/messaging-implementation.md`
