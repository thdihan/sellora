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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_code_id')->constrained('tax_codes')->onDelete('cascade');
            $table->string('label', 100); // e.g., "Standard VAT", "Reduced VAT"
            $table->decimal('percent', 5, 2); // Tax percentage (0.00 to 100.00)
            $table->string('country', 2)->nullable(); // ISO country code
            $table->string('region', 100)->nullable(); // State/province/region
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['tax_code_id', 'is_active']);
            $table->index(['effective_from', 'effective_to']);
            $table->index(['country', 'region']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
