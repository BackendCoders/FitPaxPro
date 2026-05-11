<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercise_library_items', function (Blueprint $table) {
            if (!Schema::hasColumn('exercise_library_items', 'source_exercise_id')) {
                $table->string('source_exercise_id', 100)->nullable()->after('exercise_name');
            }

            if (!Schema::hasColumn('exercise_library_items', 'source_slug')) {
                $table->string('source_slug', 150)->nullable()->after('source_exercise_id');
            }

            if (!Schema::hasColumn('exercise_library_items', 'body_part')) {
                $table->string('body_part', 100)->nullable()->after('target_muscle_group');
            }

            if (!Schema::hasColumn('exercise_library_items', 'target_muscles_json')) {
                $table->json('target_muscles_json')->nullable()->after('body_part');
            }

            if (!Schema::hasColumn('exercise_library_items', 'secondary_muscles_json')) {
                $table->json('secondary_muscles_json')->nullable()->after('target_muscles_json');
            }

            if (!Schema::hasColumn('exercise_library_items', 'equipments_json')) {
                $table->json('equipments_json')->nullable()->after('secondary_muscles_json');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exercise_library_items', function (Blueprint $table) {
            $table->dropColumn([
                'source_exercise_id',
                'source_slug',
                'body_part',
                'target_muscles_json',
                'secondary_muscles_json',
                'equipments_json',
            ]);
        });
    }
};
