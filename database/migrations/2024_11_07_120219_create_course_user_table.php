<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->foreignId('allocated_by_business_id')->nullable()->constrained('businesses');
            $table->timestamp('allocated_at')->nullable();
            $table->boolean('completed')->default(false);
            $table->json('completed_sections')->nullable();
            $table->unsignedBigInteger('current_section_id')->nullable();
            $table->integer('completed_sections_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_user');
    }
};
