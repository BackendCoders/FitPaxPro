<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercise_library_items', function (Blueprint $table) {
            if (!Schema::hasColumn('exercise_library_items', 'source_image_name')) {
                $table->string('source_image_name', 255)->nullable()->after('source_slug');
            }

            if (!Schema::hasColumn('exercise_library_items', 'image_width')) {
                $table->unsignedInteger('image_width')->nullable()->after('image_path');
            }

            if (!Schema::hasColumn('exercise_library_items', 'image_height')) {
                $table->unsignedInteger('image_height')->nullable()->after('image_width');
            }

            if (!Schema::hasColumn('exercise_library_items', 'pose_landmarks_json')) {
                $table->json('pose_landmarks_json')->nullable()->after('image_height');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exercise_library_items', function (Blueprint $table) {
            $columns = [];

            foreach (['source_image_name', 'image_width', 'image_height', 'pose_landmarks_json'] as $column) {
                if (Schema::hasColumn('exercise_library_items', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
