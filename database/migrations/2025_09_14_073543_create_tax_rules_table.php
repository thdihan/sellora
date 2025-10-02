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
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->foreignId('tax_rate_id')->constrained('tax_rates')->onDelete('cascade');
            
            // Rule scope
            $table->enum('applies_to', ['all', 'category', 'product', 'shipping', 'fees'])->default('all');
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            
            // Tax calculation settings
            $table->enum('price_mode', ['INCLUSIVE', 'EXCLUSIVE'])->default('EXCLUSIVE');
            $table->enum('bearer', ['CUSTOMER', 'COMPANY'])->default('CUSTOMER');
            
            // Special tax conditions
            $table->boolean('reverse_charge')->default(false);
            $table->boolean('zero_rated')->default(false);
            $table->boolean('exempt')->default(false);
            $table->boolean('withholding')->default(false);
            $table->decimal('withholding_percent', 5, 2)->nullable();
            
            // Discount and shipping settings
            $table->enum('taxable_discounts', ['NONE', 'BEFORE_TAX', 'AFTER_TAX'])->default('NONE');
            $table->boolean('taxable_shipping')->default(true);
            
            // Place of supply and rounding
            $table->enum('place_of_supply', ['ORIGIN', 'DESTINATION', 'AUTO'])->default('ORIGIN');
            $table->enum('rounding', ['LINE', 'SUBTOTAL', 'INVOICE'])->default('LINE');
            
            // Rule priority and status
            $table->integer('priority')->default(0); // Higher number = higher priority
            $table->boolean('is_active')->default(true);
            $table->text('comments')->nullable();
            
            $table->timestamps();
            
            $table->index(['applies_to', 'is_active']);
            $table->index(['priority', 'is_active']);
            $table->index(['category_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
    }
};
