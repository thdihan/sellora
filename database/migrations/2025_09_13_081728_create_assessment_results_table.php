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
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_attempt_id')->constrained('assessment_attempts')->onDelete('cascade');
            $table->integer('question_index');
            $table->text('question_text');
            $table->json('user_answer')->nullable();
            $table->json('correct_answer')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->decimal('points_earned', 8, 2)->default(0);
            $table->decimal('max_points', 8, 2)->default(0);
            $table->integer('time_spent')->nullable(); // in seconds
            $table->text('feedback')->nullable();
            $table->timestamps();
            
            $table->index(['assessment_attempt_id', 'question_index']);
            $table->index('is_correct');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};
