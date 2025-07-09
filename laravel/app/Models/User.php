<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    
    /**
     * فیلدهای قابل mass-assignment
     */
    protected $fillable = [
        'username',             // نام کاربری
        'name',                 // نام نمایشی
        'avatar',               // آواتار
        'password',             // رمز عبور
        'secret',               // کد 2FA یا similar
        'is_online',            // وضعیت آنلاین
        'is_active',            // فعال/غیرفعال
        'last_seen',            // آخرین بازدید
        'master_key_encrypted', // کلید رمزگذاری‌شده
        'master_key_salt',      // salt برای کلید
        'master_key_iv',        // iv برای کلید
    ];
    protected $hidden = [
        'secret',
        'master_key_encrypted',
        'master_key_salt',
        'master_key_iv',
    ];
    protected $appends = ['avatar_url'];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }
    public function backupCodes()
    {
        return $this->hasMany(BackupCode::class);
    }

}
