<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

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
    protected $appends = ['signed_url'];
    public function getSignedUrlAttribute()
    {
        $expiration = now()->addMinutes(5);
        return URL::temporarySignedRoute(
            'file.local',
            $expiration,
            ['path' => $this->path]
        );
    }
    public function message(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }
}