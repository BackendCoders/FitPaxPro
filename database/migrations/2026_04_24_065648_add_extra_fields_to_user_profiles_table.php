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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('age');
            $table->enum('fitness_level', ['beginner', 'intermediate', 'advanced', 'athlete'])->nullable()->after('activity_level');
            $table->enum('preferred_workout_time', ['morning', 'afternoon', 'evening', 'late_night', 'flexible'])->nullable()->after('fitness_level');
            $table->string('blood_group', 5)->nullable()->after('preferred_workout_time');
            $table->decimal('target_weight', 5, 2)->nullable()->after('current_weight');
            $table->integer('workout_frequency_goal')->nullable()->after('goal_type');
            $table->decimal('body_fat_percentage', 5, 2)->nullable()->after('target_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'fitness_level',
                'preferred_workout_time',
                'blood_group',
                'target_weight',
                'workout_frequency_goal',
                'body_fat_percentage'
            ]);
        });
    }
};
