<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->boolean('has_expiry')->default(false)->after('issued_at');
            $table->timestamp('expires_at')->nullable()->after('has_expiry');
        });
    }

    public function down()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['has_expiry', 'expires_at']);
        });
    }
};
