<?php

/**
 * Add Price, Stock, and Expiration Date to Products Table Migration
 *
 * This migration adds price, stock, and expiration_date fields to the products table
 * to support enhanced product management functionality.
 *
 * @category Migration
 * @package  Database\Migrations
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0
 * @link     https://sellora.com
 * @since    2025-09-19
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
        Schema::table(
            'products',
            function (Blueprint $table) {
                $table->decimal('price', 10, 2)->nullable()->after('description');
                $table->integer('stock')->default(0)->after('price');
                $table->date('expiration_date')->nullable()->after('stock');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table(
            'products',
            function (Blueprint $table) {
                $table->dropColumn(['price', 'stock', 'expiration_date']);
            }
        );
    }
};
