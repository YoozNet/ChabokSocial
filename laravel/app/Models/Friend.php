<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    protected $fillable = ['user_id', 'friend_id', 'status'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function friend() {
        return $this->belongsTo(User::class, 'friend_id');
    }
    public function getPeerAttribute()
    {
        return $this->user_id === auth()->id() ? $this->friend : $this->user;
    }
}
