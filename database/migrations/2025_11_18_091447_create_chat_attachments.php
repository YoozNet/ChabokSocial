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
        Schema::create('chat_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_message_id')
                ->constrained('chat_messages')
                ->cascadeOnDelete();

            $table->enum('type', ['image', 'video', 'file']);
            $table->string('original_name', 255);
            $table->string('mime_type', 255);
            $table->unsignedBigInteger('size');
            $table->string('path', 500);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_attachments');
    }
};