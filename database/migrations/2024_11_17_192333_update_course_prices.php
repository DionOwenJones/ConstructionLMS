<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set all courses to have a default price of $99.99 and not free
        DB::table('courses')->update([
            'price' => 99.99,
            'is_free' => false
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all courses to free
        DB::table('courses')->update([
            'price' => 0.00,
            'is_free' => true
        ]);
    }
};
