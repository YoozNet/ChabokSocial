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
        Schema::create('chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_room_id')
                ->constrained('chat_rooms')
                ->cascadeOnDelete();

            $table->string('sender_nickname', 100)->nullable();
            $table->string('sender_masked_ip', 64)->nullable();
            $table->longtext('body')->nullable(); // max length handled in validation

            $table->timestamps(); // created_at = send time
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};