<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiryFieldsToCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasTable('courses')) {
            return;
        }
        
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('has_expiry')->default(false);
            $table->integer('validity_months')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (!Schema::hasTable('courses')) {
            return;
        }
        
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('has_expiry');
            $table->dropColumn('validity_months');
        });
    }
}
