<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->text('bio')->nullable();
            $table->string('city')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'non_binary', 'other'])->nullable();
            $table->string('pronouns')->nullable();
            $table->json('looking_for')->nullable();
            $table->boolean('profile_completed')->default(false);
            $table->integer('onboarding_step')->default(1);
            $table->timestamps();
        });

        // Migrar datos existentes de users a profiles
        \DB::statement("
            INSERT INTO profiles (user_id, bio, city, birth_date, gender, pronouns, looking_for, profile_completed, onboarding_step, created_at, updated_at)
            SELECT id, bio, city, birth_date, gender, pronouns, looking_for, profile_completed, onboarding_step, NOW(), NOW()
            FROM users
            WHERE bio IS NOT NULL OR city IS NOT NULL OR birth_date IS NOT NULL OR gender IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
