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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('generic_name', 200)->nullable();
            $table->string('composition', 255)->nullable();
            $table->string('dosage_form', 50)->nullable();
            $table->string('strength', 50)->nullable();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->nullable();
            $table->string('hsn', 50)->nullable();
            $table->string('schedule', 20)->nullable();
            $table->string('storage_conditions', 255)->nullable();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained('product_brands')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('product_units')->onDelete('set null');
            $table->string('pack_size', 50)->nullable();
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->integer('reorder_level')->default(0);
            $table->integer('reorder_qty')->default(0);
            $table->boolean('allow_negative')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            $table->index(['status', 'category_id']);
            $table->index(['sku', 'barcode']);
            $table->index('reorder_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};