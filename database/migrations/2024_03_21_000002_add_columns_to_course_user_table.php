<?php

namespace Database\Migrations;

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
        Schema::table('course_user', function (Blueprint $table) {
            if (!Schema::hasColumn('course_user', 'completed_sections')) {
                $table->json('completed_sections')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'completed_sections_count')) {
                $table->integer('completed_sections_count')->default(0);
            }
            if (!Schema::hasColumn('course_user', 'current_section_id')) {
                $table->unsignedBigInteger('current_section_id')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'last_accessed_at')) {
                $table->timestamp('last_accessed_at')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'completed')) {
                $table->boolean('completed')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropColumn([
                'completed_sections',
                'completed_sections_count',
                'current_section_id',
                'last_accessed_at',
                'completed_at',
                'completed'
            ]);
        });
    }
};
