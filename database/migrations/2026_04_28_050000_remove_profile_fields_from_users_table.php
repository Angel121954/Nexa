<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'city',
                'birth_date',
                'gender',
                'pronouns',
                'looking_for',
                'profile_completed',
                'onboarding_step',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable();
            $table->string('city')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'non_binary', 'other'])->nullable();
            $table->string('pronouns')->nullable();
            $table->json('looking_for')->nullable();
            $table->boolean('profile_completed')->default(false);
            $table->integer('onboarding_step')->default(1);
        });
    }
};
