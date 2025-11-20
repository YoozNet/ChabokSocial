<?php

namespace App\Services\Installer\DTOs;

final class AdminDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $username,
        public string $password,
    ) {}
}