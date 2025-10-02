<?php

/**
 * Sync Product Inventory Command
 *
 * This file contains the SyncProductInventory command for synchronizing
 * products with the inventory system.
 *
 * @category Commands
 * @package  App\Console\Commands
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\Warehouse;
use Illuminate\Console\Command;

/**
 * Class SyncProductInventory
 *
 * Synchronizes products with inventory system by creating stock balance records
 * for products that don't have them.
 *
 * @category Commands
 * @package  App\Console\Commands
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class SyncProductInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:sync-products {--warehouse-id=1 : Default warehouse ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products with inventory system by creating stock balance records';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting product inventory synchronization...');
        
        $warehouseId = $this->option('warehouse-id');
        
        // Verify warehouse exists
        $warehouse = Warehouse::find($warehouseId);
        if (!$warehouse) {
            $this->error("Warehouse with ID {$warehouseId} not found.");
            return 1;
        }
        
        $this->info("Using warehouse: {$warehouse->name} (ID: {$warehouseId})");
        
        // Get products without stock balances in the specified warehouse
        $productsWithoutStock = Product::whereDoesntHave(
            'stockBalances',
            function ($query) use ($warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            }
        )->where('status', true)->get();
        
        if ($productsWithoutStock->isEmpty()) {
            $this->info('All products already have stock balance records.');
            return 0;
        }
        
        $this->info("Found {$productsWithoutStock->count()} products without stock balance records.");
        
        $bar = $this->output->createProgressBar($productsWithoutStock->count());
        $bar->start();
        
        $syncedCount = 0;
        
        foreach ($productsWithoutStock as $product) {
            try {
                StockBalance::create(
                    [
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouseId,
                        'batch_id' => null,
                        'qty_on_hand' => 0,
                        'qty_reserved' => 0,
                    ]
                );
                
                $syncedCount++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed to sync product {$product->name} (ID: {$product->id}): {$e->getMessage()}");
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("Successfully synchronized {$syncedCount} products with inventory system.");
        
        if ($syncedCount < $productsWithoutStock->count()) {
            $failedCount = $productsWithoutStock->count() - $syncedCount;
            $this->warn("{$failedCount} products failed to sync. Check the error messages above.");
        }
        
        return 0;
    }
}