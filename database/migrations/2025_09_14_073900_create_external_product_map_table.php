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
        Schema::create('external_product_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('external_system', 100); // 'shopify', 'woocommerce', 'magento', 'amazon', etc.
            $table->string('external_id', 255); // External system's product ID
            $table->string('external_sku', 255)->nullable(); // External system's SKU
            $table->string('external_url', 500)->nullable(); // Direct link to external product
            $table->json('external_data')->nullable(); // Store additional external system data
            $table->json('field_mapping')->nullable(); // Field mapping configuration
            $table->enum('sync_direction', ['import', 'export', 'bidirectional'])->default('import');
            $table->boolean('auto_sync')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('last_sync_attempt_at')->nullable();
            $table->enum('sync_status', ['pending', 'syncing', 'success', 'failed', 'disabled'])->default('pending');
            $table->text('sync_error')->nullable();
            $table->integer('sync_attempts')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['product_id', 'external_system', 'external_id']);
            $table->index(['external_system', 'external_id']);
            $table->index(['sync_status', 'auto_sync']);
            $table->index(['last_synced_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_product_map');
    }
};
