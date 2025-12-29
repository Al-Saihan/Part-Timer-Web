<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /**
     * Get all chat rooms for the authenticated user
     */
    public function getChatRooms(Request $request)
    {
        $user = $request->user();

        $chatRooms = ChatRoom::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with([
            'participants' => function ($query) use ($user) {
                $query->where('user_id', '!=', $user->id);
            },
            'latestMessage.sender'
        ])
        ->get()
        ->map(function ($room) use ($user) {
            $otherParticipant = $room->participants->first();
            $appliedJobs = [];
            
            if ($otherParticipant) {
                // Get all jobs posted by current user that the other participant has applied to
                $appliedJobs = \App\Models\JobApplication::where('seeker_id', $otherParticipant->id)
                    ->whereHas('job', function ($query) use ($user) {
                        $query->where('recruiter_id', $user->id);
                    })
                    ->with('job:id,title')
                    ->latest()
                    ->get()
                    ->pluck('job.title')
                    ->filter()
                    ->values()
                    ->toArray();
            }
            
            return [
                'id' => $room->id,
                'other_participant' => $otherParticipant,
                'applied_jobs' => $appliedJobs,
                'latest_message' => $room->latestMessage ? [
                    'content' => $room->latestMessage->content,
                    'message_type' => $room->latestMessage->message_type,
                    'sender_name' => $room->latestMessage->sender->name,
                    'created_at' => $room->latestMessage->created_at,
                ] : null,
                'created_at' => $room->created_at,
                'updated_at' => $room->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'chat_rooms' => $chatRooms
        ]);
    }

    /**
     * Get or create a chat room with another user
     */
    public function getOrCreateChatRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'other_user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $otherUserId = $request->other_user_id;

        // Prevent creating a chat room with yourself
        if ($user->id == $otherUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot create a chat room with yourself'
            ], 422);
        }

        // Find existing chat room between these two users
        $chatRoom = ChatRoom::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereHas('participants', function ($query) use ($otherUserId) {
            $query->where('user_id', $otherUserId);
        })
        ->first();

        // Create new chat room if it doesn't exist
        if (!$chatRoom) {
            $chatRoom = DB::transaction(function () use ($user, $otherUserId) {
                $room = ChatRoom::create([]);
                
                $room->participants()->attach([$user->id, $otherUserId]);
                
                return $room;
            });
        }

        $chatRoom->load(['participants', 'latestMessage']);

        return response()->json([
            'success' => true,
            'chat_room' => [
                'id' => $chatRoom->id,
                'participants' => $chatRoom->participants,
                'latest_message' => $chatRoom->latestMessage,
                'created_at' => $chatRoom->created_at,
            ]
        ]);
    }

    /**
     * Get messages for a specific chat room
     */
    public function getMessages(Request $request, $roomId)
    {
        $user = $request->user();

        // Verify user is a participant of this room
        $chatRoom = ChatRoom::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->find($roomId);

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Chat room not found or access denied'
            ], 404);
        }

        $perPage = $request->get('per_page', 50);
        $messages = Message::where('room_id', $roomId)
            ->with('sender:id,name,profile_pic')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'messages' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ]
        ]);
    }

    /**
     * Send a message in a chat room
     */
    public function sendMessage(Request $request, $roomId)
    {
        $user = $request->user();

        // Verify user is a participant of this room
        $chatRoom = ChatRoom::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->find($roomId);

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Chat room not found or access denied'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:5000',
            'message_type' => 'sometimes|in:text,image,system'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $message = Message::create([
            'room_id' => $roomId,
            'sender_id' => $user->id,
            'content' => $request->input('content'),
            'message_type' => $request->get('message_type', 'text')
        ]);

        $message->load('sender:id,name,profile_pic');

        return response()->json([
            'success' => true,
            'message' => $message
        ], 201);
    }

    /**
     * Delete a message (soft delete or actual delete)
     */
    public function deleteMessage(Request $request, $roomId, $messageId)
    {
        $user = $request->user();

        $message = Message::where('room_id', $roomId)
            ->where('id', $messageId)
            ->where('sender_id', $user->id)
            ->first();

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Message not found or access denied'
            ], 404);
        }

        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }

    /**
     * Get chat room details
     */
    public function getChatRoomDetails(Request $request, $roomId)
    {
        $user = $request->user();

        $chatRoom = ChatRoom::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['participants'])
        ->find($roomId);

        if (!$chatRoom) {
            return response()->json([
                'success' => false,
                'message' => 'Chat room not found or access denied'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'chat_room' => [
                'id' => $chatRoom->id,
                'participants' => $chatRoom->participants,
                'created_at' => $chatRoom->created_at,
            ]
        ]);
    }
}
