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
        Schema::table('course_user', function (Blueprint $table) {
            if (!Schema::hasColumn('course_user', 'completed_sections')) {
                $table->json('completed_sections')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'completed_sections_count')) {
                $table->integer('completed_sections_count')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropColumn(['completed_sections', 'completed_sections_count']);
        });
    }
};
