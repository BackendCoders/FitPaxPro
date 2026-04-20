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
        Schema::table('membership_plan_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('membership_plan_templates', 'image')) {
                $table->string('image')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_plan_templates', function (Blueprint $table) {
            $table->dropColumn(['image']);
        });
    }
};
