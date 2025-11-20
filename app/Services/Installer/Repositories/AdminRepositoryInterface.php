<?php

namespace App\Services\Installer\Repositories;

use App\Models\Admin;

interface AdminRepositoryInterface
{
    public function createSuperAdmin(string $name, string $email, string $hashedPassword,string $username): Admin;
    public function existsByEmail(string $email): bool;
    public function existsByUsername(string $email): bool;
}