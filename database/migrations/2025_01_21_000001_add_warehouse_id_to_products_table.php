<?php

/**
 * Add Warehouse ID to Products Table Migration
 *
 * This migration adds warehouse_id field to the products table
 * to support warehouse-based product management.
 *
 * @category Migration
 * @package  Database\Migrations
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0
 * @link     https://sellora.com
 * @since    2025-01-21
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
                $table->foreignId('warehouse_id')->nullable()->after('unit_id')->constrained('warehouses')->onDelete('set null');
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
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            }
        );
    }
};