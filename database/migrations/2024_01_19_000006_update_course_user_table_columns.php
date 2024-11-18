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
            if (!Schema::hasColumn('course_user', 'completed')) {
                $table->boolean('completed')->default(false);
            }
            if (!Schema::hasColumn('course_user', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'completed_sections')) {
                $table->json('completed_sections')->nullable();
            }
            if (!Schema::hasColumn('course_user', 'completed_sections_count')) {
                $table->integer('completed_sections_count')->default(0);
            }
            if (!Schema::hasColumn('course_user', 'current_section_id')) {
                $table->foreignId('current_section_id')->nullable()->constrained('course_sections')->nullOnDelete();
            }
            if (!Schema::hasColumn('course_user', 'last_accessed_at')) {
                $table->timestamp('last_accessed_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            $columns = [
                'completed',
                'completed_at',
                'completed_sections',
                'completed_sections_count',
                'current_section_id',
                'last_accessed_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('course_user', $column)) {
                    if ($column === 'current_section_id') {
                        $table->dropForeign(['current_section_id']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
