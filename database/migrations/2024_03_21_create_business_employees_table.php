<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('business_course_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('seats_purchased');
            $table->integer('seats_allocated')->default(0);
            $table->timestamp('purchased_at');
            $table->timestamps();
        });

        Schema::create('business_course_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_course_purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_employee_id')->constrained()->onDelete('cascade');
            $table->timestamp('allocated_at');
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('business_course_allocations');
        Schema::dropIfExists('business_course_purchases');
    }
};
