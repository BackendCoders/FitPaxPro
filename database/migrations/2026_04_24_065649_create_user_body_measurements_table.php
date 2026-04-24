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
        Schema::create('user_body_measurements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->comment('Link to users table');
            $table->date('recorded_at');
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('chest', 5, 2)->nullable()->comment('in cm');
            $table->decimal('waist', 5, 2)->nullable()->comment('in cm');
            $table->decimal('hips', 5, 2)->nullable()->comment('in cm');
            $table->decimal('biceps', 5, 2)->nullable()->comment('in cm');
            $table->decimal('thighs', 5, 2)->nullable()->comment('in cm');
            $table->decimal('body_fat_percentage', 5, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_body_measurements');
    }
};
