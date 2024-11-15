<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop existing tables if they exist
        Schema::dropIfExists('business_course_allocations');
        Schema::dropIfExists('business_course_purchases');

        // Table for business course purchases
        Schema::create('business_course_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('seats_purchased')->default(1);
            $table->decimal('price_per_seat', 10, 2);
            $table->timestamp('purchased_at');
            $table->timestamps();
            
            // A business can purchase a course multiple times
            $table->index(['business_id', 'course_id', 'purchased_at'], 'bcp_unique_purchase');
        });

        // Table for business course allocations
        Schema::create('business_course_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_course_purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('allocated_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // A user can only be allocated to a purchase once
            $table->unique(['business_course_purchase_id', 'user_id'], 'bca_unique_allocation');
        });

        // Add columns to course_user table for business allocations
        if (!Schema::hasColumns('course_user', ['allocated_by_business_id', 'allocated_at'])) {
            Schema::table('course_user', function (Blueprint $table) {
                $table->foreignId('allocated_by_business_id')->nullable()->constrained('businesses')->onDelete('set null');
                $table->timestamp('allocated_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('course_user', function (Blueprint $table) {
            $table->dropForeign(['allocated_by_business_id']);
            $table->dropColumn(['allocated_by_business_id', 'allocated_at']);
        });
        
        Schema::dropIfExists('business_course_allocations');
        Schema::dropIfExists('business_course_purchases');
    }
};
