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
        Schema::create('chat_room_participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_room_id')
                ->constrained('chat_rooms')
                ->cascadeOnDelete();

            $table->string('client_id', 100); // uuid per browser-tab/client
            $table->timestamp('last_seen_at');

            $table->unique(['chat_room_id', 'client_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_room_participants');
    }
};