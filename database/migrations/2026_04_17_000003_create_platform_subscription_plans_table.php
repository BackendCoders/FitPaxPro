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
        Schema::create('platform_subscription_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('yearly_price', 10, 2)->nullable();
            $table->integer('max_gyms')->default(1);
            $table->integer('max_members')->nullable();
            $table->boolean('has_analytics')->default(false);
            $table->boolean('has_mobile_app')->default(false);
            $table->timestamps();
        });

        Schema::table('gyms', function (Blueprint $table) {
            $table->foreignUuid('platform_plan_id')->nullable()->constrained('platform_subscription_plans');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->dropForeign(['platform_plan_id']);
            $table->dropColumn('platform_plan_id');
        });
        Schema::dropIfExists('platform_subscription_plans');
    }
};
