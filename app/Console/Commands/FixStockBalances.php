<?php

/**
 * @package    App\Console\Commands
 * @author     Sellora Team
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 * @link       https://sellora.com
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\StockBalance;
use App\Models\Warehouse;

/**
 * Fix stock balances for existing products
 *
 * This command consolidates multiple stock balance records into single records
 * for products that have multiple stock balance entries.
 */
class FixStockBalances extends Command
{
    protected $signature = 'stock:fix-balances';
    protected $description = 'Fix stock balances for existing products';

    /**
     * Execute the console command
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting stock balance fix...');
        
        $defaultWarehouse = Warehouse::first();
        if (!$defaultWarehouse) {
            $this->error('No warehouse found. Please create a warehouse first.');
            return 1;
        }
        
        $products = Product::with(['stockBalances'])->get();
        $fixedCount = 0;
        
        foreach ($products as $product) {
            // Check if product has multiple stock balance records
            $stockBalances = $product->stockBalances;
            
            if ($stockBalances->count() > 1) {
                $this->info("Fixing product: {$product->name} (ID: {$product->id})");
                
                // Calculate total quantity from all stock balances
                $totalOnHand = $stockBalances->sum('qty_on_hand');
                $totalReserved = $stockBalances->sum('qty_reserved');
                
                // Delete all existing stock balances
                $product->stockBalances()->delete();
                
                // Create a single consolidated stock balance
                if ($totalOnHand > 0 || $totalReserved > 0) {
                    StockBalance::create(
                        [
                            'product_id' => $product->id,
                            'warehouse_id' => $defaultWarehouse->id,
                            'batch_id' => null,
                            'qty_on_hand' => $totalOnHand,
                            'qty_reserved' => $totalReserved,
                        ]
                    );
                }
                
                $fixedCount++;
            }
        }
        
        $this->info("Fixed {$fixedCount} products with multiple stock balance records.");
        return 0;
    }
}