<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_room_id',
        'sender_nickname',
        'sender_masked_ip',
        'body',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(ChatAttachment::class);
    }
}