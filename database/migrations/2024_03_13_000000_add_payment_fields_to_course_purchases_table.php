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
        Schema::table('course_purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('course_purchases', 'payment_id')) {
                $table->string('payment_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('course_purchases', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_purchases', function (Blueprint $table) {
            $columns = ['payment_id', 'payment_method'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('course_purchases', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
