<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('time_limit')->nullable(); // in minutes
            $table->integer('passing_score')->default(70);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('show_feedback')->default(true);
            $table->integer('max_attempts')->default(3);
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['multiple_choice', 'essay', 'matching']);
            $table->text('question_text');
            $table->json('options')->nullable(); // For multiple choice and matching
            $table->json('correct_answer');
            $table->text('feedback')->nullable();
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('answers');
            $table->integer('score');
            $table->boolean('passed');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessment_attempts');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('assessments');
    }
};
