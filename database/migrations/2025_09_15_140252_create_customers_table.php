<?php

/**
 * Create customers table migration
 *
 * PHP version 8.1
 *
 * @category Migration
 * @package  Database\Migrations
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Customer name (required)');
            $table->string('shop_name')->nullable()->comment('Shop or business name');
            $table->text('full_address')->nullable()->comment('Complete address');
            $table->string('phone')->comment('Phone number (required)');
            $table->string('email')->nullable()->comment('Email address (optional)');
            $table->text('notes')->nullable()->comment('Additional notes');
            $table->timestamps();
            
            // Indexes for search performance
            $table->index(['name']);
            $table->index(['shop_name']);
            $table->index(['phone']);
            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
