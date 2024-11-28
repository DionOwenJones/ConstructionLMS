<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Check if discount_codes table exists and has data
        if (Schema::hasTable('discount_codes')) {
            $discountCodes = DB::table('discount_codes')->get();
            
            foreach ($discountCodes as $code) {
                DB::table('discounts')->insert([
                    'code' => $code->code,
                    'description' => $code->description,
                    'discount_type' => 'percentage',
                    'discount_value' => $code->discount_percentage,
                    'start_date' => $code->valid_from,
                    'end_date' => $code->valid_until,
                    'max_uses' => $code->usage_limit,
                    'used_count' => $code->times_used,
                    'is_active' => $code->is_active,
                    'created_at' => $code->created_at,
                    'updated_at' => $code->updated_at
                ]);
            }
        }
    }

    public function down()
    {
        // No need for down migration as we're just copying data
    }
};
