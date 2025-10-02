<?php

/**
 * Update Existing Products Warehouse ID Migration
 *
 * This migration updates existing products to assign them to the main warehouse.
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
use App\Models\Warehouse;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Get the main warehouse
        $mainWarehouse = Warehouse::where('is_main', true)->first();
        
        if ($mainWarehouse) {
            // Update all products that don't have a warehouse_id
            Product::whereNull('warehouse_id')->update(['warehouse_id' => $mainWarehouse->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Set warehouse_id to null for all products
        Product::update(['warehouse_id' => null]);
    }
};