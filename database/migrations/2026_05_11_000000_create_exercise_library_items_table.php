<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_library_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('exercise_name');
            $table->string('target_muscle_group', 100)->nullable();
            $table->string('exercise_category', 100)->nullable();
            $table->string('equipment_type', 100)->nullable();
            $table->string('difficulty_level', 50)->nullable();
            $table->string('image_path')->nullable();
            $table->string('instruction_video_url', 255)->nullable();
            $table->text('instructions')->nullable();
            $table->text('tips')->nullable();
            $table->unsignedSmallInteger('sets')->nullable();
            $table->string('reps', 50)->nullable();
            $table->unsignedInteger('rest_period_seconds')->nullable();
            $table->unsignedInteger('estimated_duration_minutes')->nullable();
            $table->unsignedInteger('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_library_items');
    }
};
