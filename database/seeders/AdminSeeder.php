<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (Admin::query()->exists()) {
            return;
        }

        Admin::create([
            'name'     => 'Super Admin',
            'username' => 'admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('admin123'),
        ]);
    }
}