<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // id primary key
            $table->string('username', 50)->unique()->nullable();
            $table->string('password', 255)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('secret', 255)->nullable();
            $table->boolean('is_online')->default(0);
            $table->boolean('is_active')->default(0);
            $table->dateTime('last_login')->nullable();
            $table->dateTime('last_seen')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};