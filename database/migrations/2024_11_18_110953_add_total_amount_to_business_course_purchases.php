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
        Schema::table('business_course_purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('business_course_purchases', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->after('price_per_seat');
            }
            if (!Schema::hasColumn('business_course_purchases', 'stripe_payment_id')) {
                $table->string('stripe_payment_id')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('business_course_purchases', 'purchased_at')) {
                $table->timestamp('purchased_at')->nullable()->after('stripe_payment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_course_purchases', function (Blueprint $table) {
            $table->dropColumn(['total_amount', 'stripe_payment_id', 'purchased_at']);
        });
    }
};
