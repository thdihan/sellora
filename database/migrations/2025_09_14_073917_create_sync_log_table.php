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
        Schema::create('sync_log', function (Blueprint $table) {
            $table->id();
            $table->string('sync_type', 50); // 'product_import', 'product_export', 'inventory_sync', etc.
            $table->string('external_system', 100); // 'shopify', 'woocommerce', 'csv', 'api', etc.
            $table->string('operation', 50); // 'create', 'update', 'delete', 'bulk_import'
            $table->morphs('syncable'); // syncable_type, syncable_id for polymorphic relation
            $table->string('external_id', 255)->nullable(); // External system's ID
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'partial'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_ms')->nullable(); // Duration in milliseconds
            $table->json('request_data')->nullable(); // Original request/import data
            $table->json('response_data')->nullable(); // Response from external system
            $table->json('changes_made')->nullable(); // What fields were changed
            $table->text('error_message')->nullable();
            $table->json('error_details')->nullable(); // Detailed error information
            $table->integer('retry_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            $table->string('batch_id', 100)->nullable(); // Group related sync operations
            $table->string('user_id')->nullable(); // Who initiated the sync
            $table->string('ip_address', 45)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['sync_type', 'status']);
            $table->index(['external_system', 'operation']);
            $table->index(['batch_id', 'status']);
            $table->index(['started_at', 'completed_at']);
            $table->index(['status', 'next_retry_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_log');
    }
};
