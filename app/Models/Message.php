<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['sender_id', 'recipient_id', 'content', 'subject', 'is_read', 'parent_message_id'])]
class Message extends Model
{
    use HasFactory;

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient of the message.
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the parent message (if this is a reply).
     */
    public function parentMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'parent_message_id');
    }

    /**
     * Get all replies to this message.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Message::class, 'parent_message_id');
    }

    /**
     * Scope to get unread messages for a user.
     */
    public function scopeUnread($query, $userId)
    {
        return $query->where('recipient_id', $userId)->where('is_read', false);
    }

    /**
     * Scope to get messages between two users.
     */
    public function scopeBetweenUsers($query, $userId1, $userId2)
    {
        return $query->where(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)->where('recipient_id', $userId2)
              ->orWhere('sender_id', $userId2)->where('recipient_id', $userId1);
        });
    }
}
