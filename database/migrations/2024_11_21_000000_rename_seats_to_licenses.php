<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('business_course_purchases', function (Blueprint $table) {
            $table->renameColumn('seats_purchased', 'licenses_purchased');
            $table->renameColumn('price_per_seat', 'price_per_license');
        });
    }

    public function down()
    {
        Schema::table('business_course_purchases', function (Blueprint $table) {
            $table->renameColumn('licenses_purchased', 'seats_purchased');
            $table->renameColumn('price_per_license', 'price_per_seat');
        });
    }
};
