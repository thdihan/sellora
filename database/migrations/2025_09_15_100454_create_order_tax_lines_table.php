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
        Schema::create('order_tax_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('tax_head_id');
            $table->decimal('base_amount', 10, 2);
            $table->decimal('rate', 8, 2);
            $table->decimal('calculated_amount', 10, 2);
            $table->enum('payer', ['client', 'company']);
            $table->boolean('visible')->default(true);
            $table->timestamps();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('tax_head_id')->references('id')->on('tax_heads');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_tax_lines');
    }
};
