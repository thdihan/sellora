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
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('batch_no', 100);
            $table->date('mfg_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->decimal('mrp', 10, 2)->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->string('barcode', 100)->nullable();
            $table->timestamps();
            
            $table->unique(['product_id', 'batch_no']);
            $table->index(['exp_date', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};