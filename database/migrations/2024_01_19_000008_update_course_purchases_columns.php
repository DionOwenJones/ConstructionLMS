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
            // Drop old columns if they exist
            if (Schema::hasColumn('course_purchases', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('course_purchases', 'stripe_payment_intent_id')) {
                $table->dropColumn('stripe_payment_intent_id');
            }
            if (Schema::hasColumn('course_purchases', 'stripe_customer_id')) {
                $table->dropColumn('stripe_customer_id');
            }

            // Add new columns
            if (!Schema::hasColumn('course_purchases', 'amount_paid')) {
                $table->decimal('amount_paid', 10, 2)->after('course_id');
            }
            if (!Schema::hasColumn('course_purchases', 'payment_id')) {
                $table->string('payment_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('course_purchases', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_id');
            }
            if (!Schema::hasColumn('course_purchases', 'payment_details')) {
                $table->json('payment_details')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('course_purchases', 'purchased_at')) {
                $table->timestamp('purchased_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_purchases', function (Blueprint $table) {
            // Add back old columns
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_customer_id')->nullable();

            // Drop new columns
            $columns = [
                'amount_paid',
                'payment_id',
                'payment_method',
                'payment_details',
                'purchased_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('course_purchases', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
