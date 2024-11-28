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
        // First, check which table exists
        if (Schema::hasTable('course_sections')) {
            // Drop the existing foreign key if it exists
            Schema::table('section_content_blocks', function (Blueprint $table) {
                $table->dropForeign(['section_id']);
            });

            // Add the correct foreign key constraint
            Schema::table('section_content_blocks', function (Blueprint $table) {
                $table->foreign('section_id')
                    ->references('id')
                    ->on('course_sections')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('course_sections')) {
            Schema::table('section_content_blocks', function (Blueprint $table) {
                $table->dropForeign(['section_id']);
                $table->foreign('section_id')
                    ->references('id')
                    ->on('course_sections')
                    ->onDelete('cascade');
            });
        }
    }
};
