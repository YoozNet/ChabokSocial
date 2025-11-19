<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'password',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ChatRoomParticipant::class);
    }
}