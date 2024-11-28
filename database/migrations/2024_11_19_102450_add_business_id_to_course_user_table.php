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
            $table->foreignId('business_id')->nullable()->after('user_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('course_purchase_id')->nullable()->after('business_id')->constrained('business_course_purchases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropForeign(['course_purchase_id']);
            $table->dropColumn(['business_id', 'course_purchase_id']);
        });
    }
};
