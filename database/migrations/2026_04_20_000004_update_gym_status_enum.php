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
        // Using raw SQL because change() on enum is tricky in some Laravel versions/drivers
        \DB::statement("ALTER TABLE gyms MODIFY COLUMN status ENUM('pending', 'active', 'suspended', 'rejected', 'inactive') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::statement("ALTER TABLE gyms MODIFY COLUMN status ENUM('pending', 'active', 'suspended', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};
