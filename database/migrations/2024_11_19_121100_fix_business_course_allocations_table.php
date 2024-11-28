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
        Schema::dropIfExists('business_course_allocations');

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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_course_allocations');
    }
};
