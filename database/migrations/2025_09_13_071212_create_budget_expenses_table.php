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
        Schema::create('budget_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budgets')->onDelete('cascade');
            $table->foreignId('bill_id')->nullable()->constrained('bills')->onDelete('cascade');
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('allocated_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['budget_id', 'allocated_at']);
            $table->index(['bill_id']);
            $table->index(['expense_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_expenses');
    }
};
