<?php

/**
 * Migration to drop the product_lots table.
 *
 * This migration removes the lot-based inventory system from the database.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('product_lots');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::create(
            'product_lots',
            function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('lot_number')->unique();
                $table->decimal('purchase_price', 10, 2);
                $table->decimal('selling_price', 10, 2);
                $table->integer('initial_quantity');
                $table->integer('current_quantity');
                $table->date('expiry_date')->nullable();
                $table->date('purchase_date');
                $table->boolean('is_active')->default(true);
                $table->string('unit')->nullable();
                $table->integer('stock_alert_quantity')->default(0);
                $table->boolean('has_expiry_date')->default(false);
                $table->timestamp('archived_at')->nullable();
                $table->timestamps();
                
                $table->index(['product_id', 'is_active']);
                $table->index(['expiry_date', 'is_active']);
                $table->index('lot_number');
            }
        );
    }
};
