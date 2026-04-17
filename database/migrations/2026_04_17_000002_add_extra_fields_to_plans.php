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
            if (!Schema::hasColumn('membership_plan_templates', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('membership_plan_templates', 'features')) {
                $table->json('features')->nullable()->after('description');
            }
            if (!Schema::hasColumn('membership_plan_templates', 'tagline')) {
                $table->string('tagline')->nullable()->after('features');
            }
            if (!Schema::hasColumn('membership_plan_templates', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('membership_plan_templates', 'color_code')) {
                $table->string('color_code')->nullable()->after('tagline');
            }
        });

        Schema::table('gym_fee_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('gym_fee_plans', 'name')) {
                $table->string('name')->after('gym_id');
            }
            if (!Schema::hasColumn('gym_fee_plans', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('gym_fee_plans', 'features')) {
                $table->json('features')->nullable()->after('description');
            }
            if (!Schema::hasColumn('gym_fee_plans', 'tagline')) {
                $table->string('tagline')->nullable()->after('features');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_plan_templates', function (Blueprint $table) {
            $table->dropColumn(['description', 'features', 'tagline', 'sort_order', 'color_code']);
        });

        Schema::table('gym_fee_plans', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'features', 'tagline']);
        });
    }
};
