<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatRoomParticipant extends Model
{
    protected $fillable = ['room_id', 'user_id'];

    /**
     * Get the chat room that owns the participant.
     */
    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'room_id');
    }

    /**
     * Get the user that owns the participant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
