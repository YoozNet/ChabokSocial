<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('backup_codes', function (Blueprint $table) {
            $table->unsignedTinyInteger('slot_number')->default(1)->after('expires_at');
            $table->enum('status', ['active', 'expired'])->default('active')->after('slot_number');
            $table->string('plain_code')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('backup_codes', function (Blueprint $table) {
            $table->dropColumn(['slot_number', 'status', 'plain_code']);
        });
    }
};
