<?php

/**
 * Product Observer
 *
 * Handles automatic synchronization of product data across modules
 * when product attributes like price, stock, etc. are updated.
 *
 * @category Observer
 * @package  App\Observers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0
 * @link     https://sellora.com
 * @since    2025-01-25
 */

namespace App\Observers;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use App\Services\ProductSyncService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

/**
 * Product Observer Class
 *
 * Observes product model events and ensures data consistency
 * across inventory, pricing, and other related modules.
 *
 * @category Observer
 * @package  App\Observers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class ProductObserver
{
    /**
     * Product Synchronization Service
     *
     * @var ProductSyncService
     */
    private $syncService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->syncService = App::make(ProductSyncService::class);
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param Product $product The product instance
     *
     * @return void
     */
    public function updated(Product $product): void
    {
        // Get all changed fields
        $changes = [];
        $original = [];
        
        // Check for any dirty attributes
        $dirtyAttributes = $product->getDirty();
        
        if (empty($dirtyAttributes)) {
            return;
        }
        
        // Prepare changes and original values
        foreach ($dirtyAttributes as $field => $newValue) {
            $changes[$field] = $newValue;
            $original[$field] = $product->getOriginal($field);
        }
        
        try {
            // Use the centralized sync service for comprehensive synchronization
            $syncResults = $this->syncService->syncProductData($product, $changes, $original);
            
            Log::info('Product updated and synchronized', [
                'product_id' => $product->id,
                'changes' => $changes,
                'sync_results' => $syncResults
            ]);
            
        } catch (\Exception $e) {
            Log::error('Product synchronization failed in observer', [
                'product_id' => $product->id,
                'changes' => $changes,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Handle the Product "created" event.
     *
     * @param Product $product The product instance
     *
     * @return void
     */
    public function created(Product $product): void
    {
        try {
            // Use the centralized sync service for initial synchronization
            $syncResults = $this->syncService->syncProductData($product);
            
            Log::info('Product created and synchronized', [
                'product_id' => $product->id,
                'sync_results' => $syncResults
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to synchronize newly created product', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param Product $product The product instance
     *
     * @return void
     */
    public function deleted(Product $product): void
    {
        try {
            DB::beginTransaction();
            
            // Clean up related data
            StockBalance::where('product_id', $product->id)->delete();
            StockTransaction::where('product_id', $product->id)->delete();
            
            // Clear cache
            \Illuminate\Support\Facades\Cache::forget("product.{$product->id}");
            \Illuminate\Support\Facades\Cache::forget("product.sku.{$product->sku}");
            
            DB::commit();
            
            Log::info('Product deleted and cleaned up', [
                'product_id' => $product->id,
                'sku' => $product->sku
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to clean up deleted product', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Synchronize stock balances when product stock changes.
     *
     * @param Product $product The product instance
     * @param array $stockChange The stock change data
     * @return void
     */
    private function syncStockBalances(Product $product, array $stockChange): void
    {
        $oldStock = $stockChange['old'] ?? 0;
        $newStock = $stockChange['new'] ?? 0;
        $difference = $newStock - $oldStock;
        
        if ($difference == 0) {
            return;
        }
        
        // Get main warehouse
        $mainWarehouse = Warehouse::where('is_main', true)->first() 
            ?? Warehouse::first();
        
        if (!$mainWarehouse) {
            Log::warning('No warehouse found for stock synchronization', [
                'product_id' => $product->id
            ]);
            return;
        }
        
        // Update or create stock balance
        $stockBalance = StockBalance::firstOrCreate(
            [
                'product_id' => $product->id,
                'warehouse_id' => $mainWarehouse->id,
            ],
            [
                'qty_on_hand' => 0,
                'qty_reserved' => 0,
            ]
        );
        
        $stockBalance->qty_on_hand = max(0, $newStock);
        $stockBalance->save();
        
        // Create stock transaction record
        StockTransaction::create(
            [
                'product_id' => $product->id,
                'warehouse_id' => $mainWarehouse->id,
                'type' => $difference > 0 ? 'adjustment_in' : 'adjustment_out',
                'qty' => abs($difference),
                'reference' => 'Product stock sync',
                'reason' => 'Automatic synchronization from product update',
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
            ]
        );
    }
    
    /**
     * Log price changes for audit trail.
     *
     * @param Product $product The product instance
     * @param array $priceChange The price change data
     * @return void
     */
    private function logPriceChange(Product $product, array $priceChange): void
    {
        Log::info('Product price changed', [
            'product_id' => $product->id,
            'product_sku' => $product->sku,
            'old_price' => $priceChange['old'],
            'new_price' => $priceChange['new'],
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'timestamp' => now()
        ]);
    }
    
    /**
     * Handle product status changes.
     *
     * @param Product $product The product instance
     * @param array $statusChange The status change data
     * @return void
     */
    private function handleStatusChange(Product $product, array $statusChange): void
    {
        // If product is being deactivated, consider reserving its stock
        if (!$statusChange['new'] && $statusChange['old']) {
            Log::info('Product deactivated', [
                'product_id' => $product->id,
                'product_sku' => $product->sku,
                'current_stock' => $product->stock
            ]);
        }
    }
    
    /**
     * Log all product changes for comprehensive audit trail.
     *
     * @param Product $product The product instance
     * @param array $changes The changes array
     * @return void
     */
    private function logProductChanges(Product $product, array $changes): void
    {
        Log::info('Product synchronized', [
            'product_id' => $product->id,
            'product_sku' => $product->sku,
            'changes' => $changes,
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);
    }
}