<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, add all new columns except owner_id
        Schema::table('businesses', function (Blueprint $table) {
            if (!Schema::hasColumn('businesses', 'address')) {
                $table->string('address')->nullable()->after('company_name');
            }
            
            if (!Schema::hasColumn('businesses', 'phone')) {
                $table->string('phone')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('businesses', 'website')) {
                $table->string('website')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('businesses', 'description')) {
                $table->text('description')->nullable()->after('website');
            }
        });

        // Now handle the owner_id transition
        if (!Schema::hasColumn('businesses', 'owner_id') && Schema::hasColumn('businesses', 'user_id')) {
            // Add owner_id column without constraints first
            Schema::table('businesses', function (Blueprint $table) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('id');
            });

            // Copy data from user_id to owner_id
            DB::table('businesses')->update([
                'owner_id' => DB::raw('user_id')
            ]);

            // Now add the foreign key constraint
            Schema::table('businesses', function (Blueprint $table) {
                $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
                // Make owner_id non-nullable now that we've copied the data
                $table->unsignedBigInteger('owner_id')->nullable(false)->change();
                
                // Drop the old user_id column and its foreign key
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }

    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            if (Schema::hasColumn('businesses', 'owner_id')) {
                $table->dropForeign(['owner_id']);
                $table->dropColumn('owner_id');
            }
            
            $table->dropColumn(['address', 'phone', 'website', 'description']);
            
            if (!Schema::hasColumn('businesses', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }
};
