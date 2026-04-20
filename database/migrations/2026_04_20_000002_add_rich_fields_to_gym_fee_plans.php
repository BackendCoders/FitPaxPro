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
        Schema::table('gym_fee_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('gym_fee_plans', 'image')) {
                $table->string('image')->nullable()->after('name');
            }
            if (!Schema::hasColumn('gym_fee_plans', 'tagline')) {
                $table->string('tagline')->nullable()->after('name');
            }
            if (!Schema::hasColumn('gym_fee_plans', 'description')) {
                $table->text('description')->nullable()->after('tagline');
            }
            if (!Schema::hasColumn('gym_fee_plans', 'features')) {
                $table->json('features')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gym_fee_plans', function (Blueprint $table) {
            $table->dropColumn(['image', 'tagline', 'description', 'features']);
        });
    }
};
