<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupCode extends Model
{
    protected $fillable = ['code', 'expires_at','slot_number','status','plain_code'];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
