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
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'price')) {
                $table->decimal('price', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('courses', 'stripe_price_id')) {
                $table->string('stripe_price_id')->nullable();
            }
            if (!Schema::hasColumn('courses', 'stripe_product_id')) {
                $table->string('stripe_product_id')->nullable();
            }
            if (!Schema::hasColumn('courses', 'is_free')) {
                $table->boolean('is_free')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['price', 'stripe_price_id', 'stripe_product_id', 'is_free']);
        });
    }
};
