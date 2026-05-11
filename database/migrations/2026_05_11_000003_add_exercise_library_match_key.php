<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercise_library_items', function (Blueprint $table) {
            if (!Schema::hasColumn('exercise_library_items', 'source_match_key')) {
                $table->string('source_match_key', 150)->nullable()->unique()->after('source_slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exercise_library_items', function (Blueprint $table) {
            if (Schema::hasColumn('exercise_library_items', 'source_match_key')) {
                $table->dropUnique(['source_match_key']);
                $table->dropColumn('source_match_key');
            }
        });
    }
};
