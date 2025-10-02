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
        Schema::create('external_maps', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // entity type (products, customers, etc.)
            $table->string('external_id'); // ID from external system
            $table->unsignedBigInteger('local_id'); // ID in local system
            $table->string('source'); // source system identifier
            $table->json('metadata')->nullable(); // additional mapping data
            $table->timestamps();
            
            $table->unique(['module', 'external_id', 'source']);
            $table->index(['module', 'local_id']);
            $table->index('source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_maps');
    }
};
