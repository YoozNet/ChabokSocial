<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_message_id',
        'type',
        'original_name',
        'mime_type',
        'size',
        'path',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }
}