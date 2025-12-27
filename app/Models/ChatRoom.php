<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatRoom extends Model
{
    protected $fillable = [];

    /**
     * Get the participants for the chat room.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_room_participants', 'room_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Get the messages for the chat room.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'room_id');
    }

    /**
     * Get the latest message for the chat room.
     */
    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'room_id')->latestOfMany();
    }
}
