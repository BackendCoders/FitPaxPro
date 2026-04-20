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
            // Make columns nullable to prevent "Field doesn't have a default value" errors
            $table->string('collection_name')->nullable()->change();
            $table->string('file_name')->nullable()->change();
            $table->string('mime_type')->nullable()->change();
            $table->string('title')->nullable()->change();
            $table->text('description')->nullable()->change();
            
            // Adjust status to have a default if it doesn't (though it seemed to have one)
            // But let's ensure it's handled correctly
            if (Schema::hasColumn('gym_gallery_media', 'status')) {
                // Ensure it has a default
                $table->string('status')->default('approved')->change(); // Using string for now or keep enum if possible
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse "nullable" without knowing previous state, 
        // but typically we don't need to revert this for a fix.
    }
};
