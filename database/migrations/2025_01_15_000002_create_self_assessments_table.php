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
        Schema::create('self_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('period'); // e.g., "Q1 2025", "January 2025", "2025"
            $table->text('targets'); // Goals and targets set for the period
            $table->text('achievements'); // What was accomplished
            $table->text('problems')->nullable(); // Challenges faced
            $table->text('solutions')->nullable(); // Solutions implemented or proposed
            $table->text('market_analysis')->nullable(); // Market insights and analysis
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reviewer_comments')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'period']);
            $table->index(['status', 'submitted_at']);
            $table->unique(['user_id', 'period']); // One assessment per user per period
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('self_assessments');
    }
};