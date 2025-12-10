<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_time_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner'])->unique();
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Insert default values
        DB::table('meal_time_settings')->insert([
            [
                'meal_type' => 'breakfast',
                'start_time' => '06:00:00',
                'end_time' => '08:00:00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meal_type' => 'lunch',
                'start_time' => '11:00:00',
                'end_time' => '13:00:00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'meal_type' => 'dinner',
                'start_time' => '17:00:00',
                'end_time' => '19:00:00',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_time_settings');
    }
};
