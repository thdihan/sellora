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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->enum('entity_type', ['order', 'bill', 'budget']);
            $table->unsignedBigInteger('entity_id');
            $table->string('from_role', 50)->nullable();
            $table->string('to_role', 50)->nullable();
            $table->enum('action', ['approve', 'reject', 'forward']);
            $table->text('remarks')->nullable();
            $table->foreignId('acted_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['entity_type', 'entity_id']);
            $table->index(['entity_type', 'action']);
            $table->index('acted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
