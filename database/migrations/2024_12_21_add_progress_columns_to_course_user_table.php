<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            if (!Schema::hasColumn('course_user', 'completed_sections')) {
                $table->json('completed_sections')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'current_section_id')) {
                $table->unsignedBigInteger('current_section_id')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'last_accessed_at')) {
                $table->timestamp('last_accessed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropColumn(['completed_sections', 'current_section_id', 'last_accessed_at']);
        });
    }
};
