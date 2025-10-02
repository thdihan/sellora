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
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('price_type', 50); // 'base', 'wholesale', 'retail', 'promotional', 'customer_specific'
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('min_quantity')->default(1);
            $table->integer('max_quantity')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('customer_group', 50)->nullable(); // 'vip', 'wholesale', 'retail'
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'price_type', 'is_active']);
            $table->index(['customer_id', 'is_active']);
            $table->index(['valid_from', 'valid_to']);
            $table->index(['min_quantity', 'max_quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
