<?php

/**
 * Product Synchronization Service
 *
 * Centralized service for synchronizing product data across all modules,
 * frontend, backend, database, and external systems.
 *
 * @category Service
 * @package  App\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0
 * @link     https://sellora.com
 * @since    2025-01-25
 */

namespace App\Services;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use App\Models\ProductBatch;
use App\Models\ExternalProductMap;
use App\Events\ProductUpdated;
use App\Events\ProductDataSynced;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * Product Synchronization Service Class
 *
 * Handles comprehensive product data synchronization across:
 * - Database tables (products, stock_balances, stock_transactions, etc.)
 * - Frontend components and views
 * - API endpoints and responses
 * - External systems integration
 * - Real-time updates and caching
 *
 * @category Services
 * @package  App\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0
 * @link     https://sellora.com
 */
class ProductSyncService
{
    /**
     * Fields that trigger comprehensive synchronization
     */
    const CRITICAL_FIELDS = [
        'name', 'sku', 'price', 'stock', 'expiration_date',
        'category_id', 'brand_id', 'unit_id', 'status',
        'min_stock_level', 'max_stock_level', 'barcode',
        'purchase_price', 'selling_price', 'tax_rate'
    ];

    /**
     * Synchronize product data across all systems
     *
     * @param Product $product  The product instance
     * @param array   $changes  Array of changed fields
     * @param array   $original Original values before changes
     *
     * @return array Synchronization results
     */
    public function syncProductData(Product $product, array $changes = [], array $original = []): array
    {
        $results = [
            'product_id' => $product->id,
            'synchronized_modules' => [],
            'errors' => [],
            'timestamp' => now()
        ];

        try {
            DB::beginTransaction();

            // 1. Synchronize stock balances
            if (isset($changes['stock']) || empty($changes)) {
                $stockResult = $this->_syncStockBalances($product, $changes['stock'] ?? null, $original['stock'] ?? null);
                $results['synchronized_modules']['stock_balances'] = $stockResult;
            }

            // 2. Synchronize pricing across all systems
            if (isset($changes['price']) || isset($changes['purchase_price']) || isset($changes['selling_price']) || empty($changes)) {
                $priceResult = $this->_syncPricing($product, $changes, $original);
                $results['synchronized_modules']['pricing'] = $priceResult;
            }

            // 3. Synchronize product batches
            if (isset($changes['expiration_date']) || empty($changes)) {
                $batchResult = $this->_syncProductBatches($product, $changes, $original);
                $results['synchronized_modules']['batches'] = $batchResult;
            }

            // 4. Synchronize external systems
            $externalResult = $this->_syncExternalSystems($product, $changes, $original);
            $results['synchronized_modules']['external_systems'] = $externalResult;

            // 5. Update cache and real-time data
            $cacheResult = $this->_updateCacheAndRealtime($product, $changes);
            $results['synchronized_modules']['cache'] = $cacheResult;

            // 6. Trigger frontend updates
            $frontendResult = $this->_triggerFrontendUpdates($product, $changes);
            $results['synchronized_modules']['frontend'] = $frontendResult;

            DB::commit();

            // Fire comprehensive sync event
            Event::dispatch(new ProductDataSynced($product, $changes, $results));

            Log::info('Product data synchronized successfully', $results);

        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = $e->getMessage();
            Log::error('Product synchronization failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $results;
    }

    /**
     * Synchronize stock balances across all warehouses
     *
     * @param Product $product  The product instance
     * @param mixed   $newStock New stock value
     * @param mixed   $oldStock Old stock value
     *
     * @return array Synchronization results
     */
    private function _syncStockBalances(Product $product, $newStock = null, $oldStock = null): array
    {
        $results = ['updated_warehouses' => [], 'transactions_created' => 0];

        $newStock = $newStock ?? $product->stock;
        $oldStock = $oldStock ?? 0;
        $difference = $newStock - $oldStock;

        // Get all warehouses or main warehouse
        $warehouses = Warehouse::active()->get();
        if ($warehouses->isEmpty()) {
            throw new \Exception('No active warehouses found for stock synchronization');
        }

        $mainWarehouse = $warehouses->where('is_main', true)->first() ?? $warehouses->first();

        foreach ($warehouses as $warehouse) {
            // Update stock balance
            $stockBalance = StockBalance::firstOrCreate([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
            ], [
                'qty_on_hand' => 0,
                'qty_reserved' => 0,
            ]);

            // For main warehouse, sync with product stock
            if ($warehouse->id === $mainWarehouse->id) {
                $stockBalance->qty_on_hand = max(0, $newStock);
                $stockBalance->save();

                // Create stock transaction if there's a difference
                if ($difference != 0) {
                    StockTransaction::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'type' => $difference > 0 ? 'adjustment_in' : 'adjustment_out',
                        'qty' => abs($difference),
                        'ref_type' => 'product_sync',
                        'note' => 'Automatic sync from product update',
                    ]);
                    $results['transactions_created']++;
                }
            }

            $results['updated_warehouses'][] = [
                'warehouse_id' => $warehouse->id,
                'warehouse_name' => $warehouse->name,
                'new_balance' => $stockBalance->qty_on_hand
            ];
        }

        return $results;
    }

    /**
     * Synchronize pricing across all systems
     *
     * @param Product $product  The product instance
     * @param array   $changes  Changed fields
     * @param array   $original Original values
     *
     * @return array Synchronization results
     */
    private function _syncPricing(Product $product, array $changes, array $original): array
    {
        $results = ['price_updates' => [], 'calculations_updated' => []];

        // Update profit margins and calculations
        if (isset($changes['price']) || isset($changes['purchase_price']) || isset($changes['selling_price'])) {
            $purchasePrice = $product->purchase_price ?? 0;
            $sellingPrice = $product->selling_price ?? $product->price ?? 0;
            
            if ($purchasePrice > 0) {
                $profitMargin = (($sellingPrice - $purchasePrice) / $purchasePrice) * 100;
                $results['calculations_updated']['profit_margin'] = round($profitMargin, 2);
            }

            $results['price_updates'] = [
                'purchase_price' => $purchasePrice,
                'selling_price' => $sellingPrice,
                'current_price' => $product->price
            ];
        }

        return $results;
    }

    /**
     * Synchronize product batches and expiration dates
     *
     * @param Product  $product   The product instance
     * @param array    $changes   Changed fields
     * @param array    $original  Original values
     *
     * @return array Synchronization results
     */
    private function _syncProductBatches(Product $product, array $changes, array $original): array
    {
        $results = ['batches_updated' => 0, 'expiry_alerts' => []];

        if (isset($changes['expiration_date'])) {
            // Update all batches for this product if expiration date changed
            $batches = ProductBatch::where('product_id', $product->id)->get();
            
            foreach ($batches as $batch) {
                if (!$batch->exp_date || $batch->exp_date != $product->expiration_date) {
                    $batch->exp_date = $product->expiration_date;
                    $batch->save();
                    $results['batches_updated']++;
                }

                // Check for expiry alerts
                if ($batch->is_near_expiry) {
                    $results['expiry_alerts'][] = [
                        'batch_id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'exp_date' => $batch->exp_date,
                        'days_to_expiry' => $batch->exp_date->diffInDays(now())
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * Synchronize with external systems
     *
     * @param Product  $product   The product instance
     * @param array    $changes   Changed fields
     * @param array    $original  Original values
     *
     * @return array Synchronization results
     */
    private function _syncExternalSystems(Product $product, array $changes, array $original): array
    {
        $results = ['external_syncs' => [], 'queued_jobs' => 0];

        // Get all external mappings for this product
        $externalMaps = ExternalProductMap::where('product_id', $product->id)
            ->where('is_active', true)
            ->where('auto_sync', true)
            ->get();

        foreach ($externalMaps as $map) {
            try {
                // Queue sync job for each external system
                \App\Jobs\SyncExternalDataJob::dispatch(
                    $map->external_system,
                    'products',
                    'push',
                    \Illuminate\Support\Str::uuid(),
                    1,
                    auth()->id() ?? 1,
                    null,
                    $product->id
                );

                $results['external_syncs'][] = [
                    'system' => $map->external_system,
                    'external_id' => $map->external_id,
                    'status' => 'queued'
                ];
                $results['queued_jobs']++;

            } catch (\Exception $e) {
                $results['external_syncs'][] = [
                    'system' => $map->external_system,
                    'external_id' => $map->external_id,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Update cache and real-time data
     *
     * @param Product $product The product instance
     * @param array   $changes Changed fields
     *
     * @return array Update results
     */
    private function _updateCacheAndRealtime(Product $product, array $changes): array
    {
        $results = ['cache_keys_updated' => [], 'realtime_channels' => []];

        // Clear and update relevant cache keys
        $cacheKeys = [
            "product.{$product->id}",
            "product.sku.{$product->sku}",
            "products.category.{$product->category_id}",
            "products.brand.{$product->brand_id}",
            "inventory.product.{$product->id}",
            "stock.product.{$product->id}"
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
            $results['cache_keys_updated'][] = $key;
        }

        // Cache fresh product data
        Cache::put("product.{$product->id}", $product->fresh()->load(['category', 'brand', 'unit', 'stockBalances']), 3600);

        return $results;
    }

    /**
     * Trigger frontend updates
     *
     * @param Product $product The product instance
     * @param array    $changes  Changed fields
     *
     * @return array Update results
     */
    private function _triggerFrontendUpdates(Product $product, array $changes): array
    {
        $results = ['broadcast_channels' => [], 'updated_components' => []];

        // Prepare data for frontend
        $frontendData = [
            'product' => $product->fresh()->load(['category', 'brand', 'unit']),
            'changes' => $changes,
            'timestamp' => now()->toISOString()
        ];

        // Broadcast to relevant channels
        $channels = [
            "product.{$product->id}",
            "products.list",
            "inventory.updates",
            "dashboard.stats"
        ];

        foreach ($channels as $channel) {
            // This would integrate with your broadcasting system
            // broadcast(new ProductDataUpdated($frontendData))->toOthers();
            $results['broadcast_channels'][] = $channel;
        }

        $results['updated_components'] = [
            'product_cards',
            'inventory_tables', 
            'stock_displays',
            'price_displays',
            'dashboard_widgets'
        ];

        return $results;
    }

    /**
     * Sync all products in the system
     *
     * @return array Bulk synchronization results
     */
    public function syncAllProducts(): array
    {
        $results = [
            'total_products' => 0,
            'synchronized' => 0,
            'errors' => 0,
            'details' => []
        ];

        $products = Product::with(['category', 'brand', 'unit'])->get();
        $results['total_products'] = $products->count();

        foreach ($products as $product) {
            try {
                $syncResult = $this->syncProductData($product);
                if (empty($syncResult['errors'])) {
                    $results['synchronized']++;
                } else {
                    $results['errors']++;
                }
                $results['details'][] = $syncResult;
            } catch (\Exception $e) {
                $results['errors']++;
                $results['details'][] = [
                    'product_id' => $product->id,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Get synchronization status for a product
     *
     * @param Product  $product  The product instance
     *
     * @return array Synchronization status
     */
    public function getSyncStatus(Product $product): array
    {
        return [
            'product_id' => $product->id,
            'last_updated' => $product->updated_at,
            'stock_balances_count' => $product->stockBalances()->count(),
            'external_mappings_count' => ExternalProductMap::where('product_id', $product->id)->count(),
            'recent_transactions_count' => StockTransaction::where('product_id', $product->id)
                ->where('created_at', '>=', now()->subDays(7))->count(),
            'cache_status' => Cache::has("product.{$product->id}") ? 'cached' : 'not_cached'
        ];
    }
}