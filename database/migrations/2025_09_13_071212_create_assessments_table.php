<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->enum('type', ['quiz', 'survey', 'exam'])->default('quiz');
            $table->json('questions')->nullable();
            $table->enum('scoring_method', ['points', 'percentage'])->default('points');
            $table->integer('max_score')->default(100);
            $table->integer('passing_score')->default(60);
            $table->integer('time_limit')->nullable(); // in minutes
            $table->integer('attempts_allowed')->default(1);
            $table->boolean('is_active')->default(true);
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->text('instructions')->nullable();
            $table->json('tags')->nullable();
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium');
            $table->integer('estimated_duration')->nullable(); // in minutes
            $table->boolean('auto_grade')->default(true);
            $table->boolean('show_results_immediately')->default(true);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('allow_review')->default(true);
            $table->string('certificate_template')->nullable();
            $table->text('completion_message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
