<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Changing file_type from ENUM to VARCHAR to support 'youtube' and other future types
        // Using raw SQL for MariaDB/MySQL compatibility without needing doctrine/dbal
        DB::statement("ALTER TABLE `gym_gallery_media` MODIFY COLUMN `file_type` VARCHAR(50) NOT NULL DEFAULT 'image'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting back to ENUM if needed, but 'youtube' records will be truncated or cause errors
        DB::statement("ALTER TABLE `gym_gallery_media` MODIFY COLUMN `file_type` ENUM('image', 'video', '360_view', 'document') NOT NULL DEFAULT 'image'");
    }
};
