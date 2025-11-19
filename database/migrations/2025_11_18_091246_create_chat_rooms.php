<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table): void {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();

            // ❗ Plain text password (per your decision)
            $table->string('password', 255)->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};