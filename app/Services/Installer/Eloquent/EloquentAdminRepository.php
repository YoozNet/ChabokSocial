<?php

namespace App\Services\Installer\Eloquent;

use App\Services\Installer\Repositories\AdminRepositoryInterface;
use App\Models\Admin;

final class EloquentAdminRepository implements AdminRepositoryInterface
{
    public function createSuperAdmin(string $name, string $email, string $hashedPassword, string $username): Admin
    {
        return Admin::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'username' => $username,
                'password' => $hashedPassword,
            ]
        );
    }

    public function existsByEmail(string $email): bool
    {
        return Admin::where('email', $email)->exists();
    }
    public function existsByUsername(string $username): bool
    {
        return Admin::where('username', $username)->exists();
    }
    
}