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
        Schema::table('users', function (Blueprint $table) {

            // Ubicación principal
            $table->decimal('home_latitude', 10, 7)->nullable();
            $table->decimal('home_longitude', 10, 7)->nullable();
            $table->string('home_city')->nullable();
            $table->string('home_country')->nullable();

            // Ubicación actual
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->string('current_city')->nullable();
            $table->string('current_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([

                'home_latitude',
                'home_longitude',
                'home_city',
                'home_country',

                'current_latitude',
                'current_longitude',
                'current_city',
                'current_country',

            ]);
        });
    }
};