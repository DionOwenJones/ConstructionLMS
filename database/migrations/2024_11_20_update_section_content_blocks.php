<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('section_content_blocks', function (Blueprint $table) {
            // Drop the existing content column
            $table->dropColumn('content');
            
            // Add specific columns for each content type
            $table->text('text_content')->nullable();
            $table->string('video_url', 1000)->nullable();
            $table->string('video_title')->nullable();
            $table->string('image_path')->nullable();
            $table->json('quiz_data')->nullable();
        });
    }

    public function down()
    {
        Schema::table('section_content_blocks', function (Blueprint $table) {
            $table->json('content');
            $table->dropColumn(['text_content', 'video_url', 'video_title', 'image_path', 'quiz_data']);
        });
    }
};
