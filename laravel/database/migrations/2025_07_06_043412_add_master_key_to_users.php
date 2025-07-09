<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('master_key_encrypted', 1024)->nullable();
            $table->string('master_key_salt', 512)->nullable();
            $table->string('master_key_iv', 512)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('master_key_encrypted');
            $table->dropColumn('master_key_salt');
            $table->dropColumn('master_key_iv');
        });
    }
};
