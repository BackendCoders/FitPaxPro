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
        Schema::table('gym_gallery_media', function (Blueprint $table) {
            // Check if media_type exists to rename it, otherwise add file_type
            if (Schema::hasColumn('gym_gallery_media', 'media_type')) {
                $table->renameColumn('media_type', 'file_type');
            } elseif (!Schema::hasColumn('gym_gallery_media', 'file_type')) {
                $table->string('file_type')->after('file_path')->default('image');
            }
            
            // Ensure order_index exists as it's cast in the model but might be missing
            if (!Schema::hasColumn('gym_gallery_media', 'order_index')) {
                $table->integer('order_index')->default(0);
            }

            // Ensure is_main_video exists as it's cast in the model
            if (!Schema::hasColumn('gym_gallery_media', 'is_main_video')) {
                $table->boolean('is_main_video')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gym_gallery_media', function (Blueprint $table) {
            if (Schema::hasColumn('gym_gallery_media', 'file_type')) {
                $table->renameColumn('file_type', 'media_type');
            }
        });
    }
};
