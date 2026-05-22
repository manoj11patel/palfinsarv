<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MessageManagementController extends Controller
{
    /**
     * Display list of messages for admin.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $searchQuery = $request->query('search');
        $senderId = $request->query('sender_id');

        $query = Message::where('recipient_id', $user->id)
            ->where('parent_message_id', null)
            ->with(['sender', 'recipient']);

        if ($searchQuery) {
            $query->where('content', 'like', '%' . $searchQuery . '%')
                  ->orWhere('subject', 'like', '%' . $searchQuery . '%');
        }

        if ($senderId) {
            $query->where('sender_id', $senderId);
        }

        $messages = $query->orderByDesc('created_at')->paginate(15);

        $agents = User::where('role', 'agent')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.messages.index', compact('messages', 'agents', 'searchQuery', 'senderId'));
    }

    /**
     * Display a single message thread.
     */
    public function show(Message $message): View
    {
        $user = auth()->user();

        // Authorization check
        if ($message->recipient_id !== $user->id && $message->sender_id !== $user->id) {
            abort(403, 'Unauthorized access to this message.');
        }

        // Mark as read if user is the recipient
        if ($message->recipient_id === $user->id && !$message->is_read) {
            $message->update(['is_read' => true]);
        }

        // Get full conversation thread
        $parentMessage = $message->parent_message_id ? $message->parentMessage : $message;
        
        $thread = Message::where(function ($query) use ($parentMessage) {
            $query->where('id', $parentMessage->id)
                  ->orWhere('parent_message_id', $parentMessage->id);
        })
        ->with(['sender', 'recipient', 'replies.sender'])
        ->orderBy('created_at')
        ->get();

        $otherUser = $message->sender_id === $user->id ? $message->recipient : $message->sender;

        return view('admin.messages.show', compact('thread', 'message', 'otherUser', 'parentMessage'));
    }

    /**
     * Store a reply to a message.
     */
    public function reply(Request $request, Message $message): RedirectResponse
    {
        $user = auth()->user();

        // Authorization check
        if ($message->recipient_id !== $user->id && $message->sender_id !== $user->id) {
            abort(403, 'Unauthorized to reply to this message.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:1',
        ]);

        $parentMsg = $message->parent_message_id ? $message->parentMessage : $message;

        Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $message->sender_id === $user->id ? $message->recipient_id : $message->sender_id,
            'content' => $validated['content'],
            'parent_message_id' => $parentMsg->id,
        ]);

        return redirect()->route('admin.messages.show', $parentMsg->id)
                        ->with('success', 'Reply sent successfully.');
    }

    /**
     * Get unread message count for sidebar notification.
     */
    public function getUnreadCount(): int
    {
        $user = auth()->user();
        return Message::where('recipient_id', $user->id)
                      ->where('is_read', false)
                      ->count();
    }
}
