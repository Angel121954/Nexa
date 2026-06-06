<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->timestamp('user1_deleted_at')->nullable()->after('created_at');
            $table->timestamp('user2_deleted_at')->nullable()->after('user1_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['user1_deleted_at', 'user2_deleted_at']);
        });
    }
};
