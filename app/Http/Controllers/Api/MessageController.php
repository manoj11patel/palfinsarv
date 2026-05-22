<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Send a message from agent to admin or admin to agent.
     */
    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'content' => 'required|string|min:1',
            'subject' => 'nullable|string|max:255',
            'parent_message_id' => 'nullable|integer|exists:messages,id',
        ]);

        $sender = Auth::user();
        $recipient = User::find($validated['recipient_id']);

        // Validate that agent can only send to admin, and admin can send to agent
        if ($sender->role === 'agent' && $recipient->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Agents can only send messages to admin.',
            ], 403);
        }

        if ($sender->role === 'admin' && $recipient->role !== 'agent') {
            return response()->json([
                'success' => false,
                'message' => 'Admins can only send messages to agents.',
            ], 403);
        }

        $message = Message::create([
            'sender_id' => $sender->id,
            'recipient_id' => $validated['recipient_id'],
            'subject' => $validated['subject'] ?? null,
            'content' => $validated['content'],
            'parent_message_id' => $validated['parent_message_id'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $message->load('sender', 'recipient'),
        ], 201);
    }

    /**
     * Get messages received by the authenticated user.
     */
    public function getReceivedMessages(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->query('per_page', 20);
        $senderId = $request->query('sender_id');

        $query = Message::where('recipient_id', $user->id)
            ->where('parent_message_id', null)
            ->with(['sender', 'recipient', 'replies.sender']);

        if ($senderId) {
            $query->where('sender_id', $senderId);
        }

        $messages = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ], 200);
    }

    /**
     * Get messages sent by the authenticated user.
     */
    public function getSentMessages(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = $request->query('per_page', 20);
        $recipientId = $request->query('recipient_id');

        $query = Message::where('sender_id', $user->id)
            ->where('parent_message_id', null)
            ->with(['sender', 'recipient']);

        if ($recipientId) {
            $query->where('recipient_id', $recipientId);
        }

        $messages = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $messages,
        ], 200);
    }

    /**
     * Get a conversation thread between two users.
     */
    public function getConversation(Request $request, $userId): JsonResponse
    {
        $authUser = Auth::user();
        $otherUser = User::find($userId);

        if (!$otherUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $messages = Message::where(function ($query) use ($authUser, $otherUser) {
            $query->where('sender_id', $authUser->id)->where('recipient_id', $otherUser->id)
                  ->orWhere('sender_id', $otherUser->id)->where('recipient_id', $authUser->id);
        })
        ->with(['sender', 'recipient', 'parentMessage', 'replies.sender'])
        ->orderBy('created_at')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ], 200);
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Request $request, $messageId): JsonResponse
    {
        $message = Message::find($messageId);

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found.',
            ], 404);
        }

        if ($message->recipient_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $message->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read.',
        ], 200);
    }

    /**
     * Get count of unread messages for authenticated user.
     */
    public function getUnreadCount(): JsonResponse
    {
        $user = Auth::user();
        $unreadCount = Message::unread($user->id)->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ], 200);
    }

    /**
     * Get unread messages for authenticated user.
     */
    public function getUnreadMessages(): JsonResponse
    {
        $user = Auth::user();
        $unreadMessages = Message::unread($user->id)
            ->where('parent_message_id', null)
            ->with(['sender', 'recipient'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $unreadMessages,
        ], 200);
    }

    /**
     * Get all agents for admin (to display in dropdown or list).
     */
    public function getAgents(): JsonResponse
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admins can access this endpoint.',
            ], 403);
        }

        $agents = User::where('role', 'agent')
            ->with('agentProfile')
            ->get()
            ->map(fn ($agent) => [
                'id'            => $agent->id,
                'name'          => $agent->name,
                'email'         => $agent->email,
                'employee_code' => $agent->agentProfile?->employee_code,
                'phone'         => $agent->agentProfile?->phone,
                'date_of_birth' => $agent->agentProfile?->date_of_birth?->format('Y-m-d'),
                'age'           => $agent->agentProfile?->date_of_birth?->age,
                'is_active'     => $agent->agentProfile?->is_active,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $agents,
        ], 200);
    }
}
