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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label');
            $table->string('model_type'); // e.g., App\Models\Gym
            $table->string('type')->default('text'); // text, number, select, etc.
            $table->text('validation_rules')->nullable();
            $table->json('options')->nullable(); // For select, radio, checkbox
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();
            $table->string('default_value')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
