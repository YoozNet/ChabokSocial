<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'user_id', 'device', 'ip_address', 'agent', 'is_online', 'last_activity','session_id'
    ];
}
