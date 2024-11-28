<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the redundant employees table if it exists
        Schema::dropIfExists('employees');

        // Update the users table to include employee-specific fields
        Schema::table('users', function (Blueprint $table) {
            // Make sure role enum includes all necessary roles
            $table->dropColumn('role');
            $table->enum('role', ['admin', 'business', 'user'])->default('user')->after('password');
            
            // Add any additional user fields that might be needed
            $table->string('job_title')->nullable()->after('role');
            $table->string('phone')->nullable()->after('job_title');
        });

        // Ensure business_employees table has all necessary fields
        if (!Schema::hasTable('business_employees')) {
            Schema::create('business_employees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('business_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('department')->nullable();
                $table->string('employee_id')->nullable(); // For business's internal employee ID
                $table->boolean('is_active')->default(true);
                $table->timestamp('joined_at')->useCurrent();
                $table->timestamp('left_at')->nullable();
                $table->timestamps();
                $table->unique(['business_id', 'user_id']);
            });
        } else {
            Schema::table('business_employees', function (Blueprint $table) {
                if (!Schema::hasColumn('business_employees', 'department')) {
                    $table->string('department')->nullable();
                }
                if (!Schema::hasColumn('business_employees', 'employee_id')) {
                    $table->string('employee_id')->nullable();
                }
                if (!Schema::hasColumn('business_employees', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (!Schema::hasColumn('business_employees', 'joined_at')) {
                    $table->timestamp('joined_at')->useCurrent();
                }
                if (!Schema::hasColumn('business_employees', 'left_at')) {
                    $table->timestamp('left_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Revert the changes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->enum('role', ['admin', 'business', 'user'])->default('user');
            $table->dropColumn(['job_title', 'phone']);
        });

        // Revert the changes to business_employees table
        Schema::table('business_employees', function (Blueprint $table) {
            $table->dropColumn(['department', 'employee_id', 'is_active', 'joined_at', 'left_at']);
        });
    }
};
