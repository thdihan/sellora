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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('product_batches')->onDelete('cascade');
            $table->integer('qty');
            $table->enum('type', [
                'opening',
                'purchase_in',
                'transfer_in',
                'transfer_out',
                'sale_reserve',
                'release_reserve',
                'sale_dispatch',
                'sale_return',
                'adjustment_in',
                'adjustment_out'
            ]);
            $table->string('ref_type', 50)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'type', 'created_at']);
            $table->index(['ref_type', 'ref_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};