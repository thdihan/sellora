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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('period_type', ['monthly', 'quarterly', 'half_yearly', 'yearly', 'custom']);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('allocated_amount', 12, 2)->default(0);
            $table->decimal('spent_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);
            $table->enum('status', ['draft', 'pending', 'approved', 'active', 'completed', 'cancelled', 'exceeded'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('categories')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('auto_approve_limit', 10, 2)->nullable();
            $table->decimal('notification_threshold', 5, 2)->default(80.00);
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_frequency', ['monthly', 'quarterly', 'half_yearly', 'yearly'])->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index(['period_type', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
