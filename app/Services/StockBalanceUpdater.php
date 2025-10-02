<?php

/**
 * Stock Balance Updater Service
 *
 * This service handles stock balance calculations, recalculations,
 * and provides various stock reporting and analysis functions.
 *
 * @category Services
 * @package  App\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  1.0.0
 * @since    1.0.0
 * @link     https://sellora.com
 */

namespace App\Services;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

/**
 * Class StockBalanceUpdater
 *
 * Manages stock balance calculations and provides stock analysis functionality.
 *
 * @category Services
 * @package  App\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @version  1.0.0
 * @since    1.0.0
 * @link     https://sellora.com
 */
class StockBalanceUpdater
{
    /**
     * Recalculate stock balances for a specific product
     *
     * @param Product $product The product to recalculate stock for
     *
     * @return void
     */
    public function recalculateProductStock(Product $product): void
    {
        $transactions = StockTransaction::where(
            'product_id',
            $product->id
        )->orderBy('created_at')
            ->get();
            
        // Group transactions by warehouse and batch
        $grouped = $transactions->groupBy(function ($transaction) {
            return $transaction->warehouse_id . '_' . ($transaction->batch_id ?? 'null');
        });
        
        foreach ($grouped as $key => $transactionGroup) {
            $parts = explode('_', $key);
            $warehouseId = $parts[0];
            $batchId = $parts[1] === 'null' ? null : $parts[1];
            
            $this->recalculateStockBalance($product->id, $warehouseId, $batchId, $transactionGroup);
        }
    }
    
    /**
     * Recalculate stock balances for a specific warehouse
     *
     * @param Warehouse $warehouse The warehouse to recalculate stock for
     *
     * @return void
     */
    public function recalculateWarehouseStock(Warehouse $warehouse): void
    {
        $transactions = StockTransaction::where(
            'warehouse_id',
            $warehouse->id
        )->orderBy('created_at')
            ->get();
            
        // Group transactions by product and batch
        $grouped = $transactions->groupBy(function ($transaction) {
            return $transaction->product_id . '_' . ($transaction->batch_id ?? 'null');
        });
        
        foreach ($grouped as $key => $transactionGroup) {
            $parts = explode('_', $key);
            $productId = $parts[0];
            $batchId = $parts[1] === 'null' ? null : $parts[1];
            
            $this->recalculateStockBalance($productId, $warehouse->id, $batchId, $transactionGroup);
        }
    }
    
    /**
     * Recalculate all stock balances
     *
     * Clears existing stock balances and recalculates from transactions.
     *
     * @return void
     */
    public function recalculateAllStock(): void
    {
        // Clear existing stock balances
        StockBalance::truncate();
        
        $transactions = StockTransaction::orderBy('created_at')->get();
        
        // Group transactions by product, warehouse, and batch
        $grouped = $transactions->groupBy(function ($transaction) {
            return $transaction->product_id . '_' . $transaction->warehouse_id . '_' . ($transaction->batch_id ?? 'null');
        });
        
        foreach ($grouped as $key => $transactionGroup) {
            $parts = explode('_', $key);
            $productId = $parts[0];
            $warehouseId = $parts[1];
            $batchId = $parts[2] === 'null' ? null : $parts[2];
            
            $this->recalculateStockBalance($productId, $warehouseId, $batchId, $transactionGroup);
        }
    }
    
    /**
     * Recalculate stock balance for specific combination
     *
     * @param int        $productId    The product ID
     * @param int        $warehouseId  The warehouse ID
     * @param int|null   $batchId      The batch ID (optional)
     * @param Collection $transactions Collection of transactions to process
     *
     * @return void
     */
    protected function recalculateStockBalance(int $productId, int $warehouseId, ?int $batchId, Collection $transactions): void
    {
        $totalQuantity = 0;
        
        foreach ($transactions as $transaction) {
            if ($transaction->type === 'inbound') {
                $totalQuantity += $transaction->quantity;
            } else {
                $totalQuantity -= $transaction->quantity;
            }
        }
        
        // Update or create stock balance
        StockBalance::updateOrCreate(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'batch_id' => $batchId,
            ],
            [
                'quantity' => max(0, $totalQuantity), // Ensure non-negative
                'reserved_quantity' => 0, // Reset reserved quantity during recalculation
            ]
        );
    }
    
    /**
     * Update stock levels for low stock alerts
     *
     * Returns statistics about stock levels across all products.
     *
     * @return array Array containing stock statistics
     */
    public function updateStockLevels(): array
    {
        $products = Product::with(['stockBalances'])->get();
        $stats = [
            'total_products' => $products->count(),
            'low_stock' => 0,
            'out_of_stock' => 0,
            'sufficient_stock' => 0,
        ];
        
        foreach ($products as $product) {
            $totalStock = $product->stockBalances->sum('qty_on_hand');
            
            if ($totalStock <= 0) {
                $stats['out_of_stock']++;
            } elseif ($totalStock <= $product->min_stock_level) {
                $stats['low_stock']++;
            } else {
                $stats['sufficient_stock']++;
            }
        }
        
        return $stats;
    }
    
    /**
     * Get stock summary by warehouse
     *
     * Returns aggregated stock data grouped by warehouse.
     *
     * @return Collection Collection of warehouse stock summaries
     */
    public function getStockSummaryByWarehouse(): Collection
    {
        return StockBalance::select(
            'warehouse_id',
            DB::raw('COUNT(DISTINCT product_id) as product_count'),
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(reserved_quantity) as total_reserved')
        )
            ->with('warehouse')
            ->groupBy('warehouse_id')
            ->get();
    }
    
    /**
     * Get stock summary by product category
     *
     * Returns aggregated stock data grouped by product category.
     *
     * @return Collection Collection of category stock summaries
     */
    public function getStockSummaryByCategory(): Collection
    {
        return StockBalance::select(
            'products.category_id',
            DB::raw('COUNT(DISTINCT stock_balances.product_id) as product_count'),
            DB::raw('SUM(stock_balances.quantity) as total_quantity'),
            DB::raw('SUM(stock_balances.quantity * products.purchase_price) as total_value')
        )
            ->join('products', 'stock_balances.product_id', '=', 'products.id')
            ->with(['product.category'])
            ->groupBy('products.category_id')
            ->get();
    }
    
    /**
     * Get products with low stock
     *
     * @param int $limit Maximum number of products to return
     *
     * @return Collection Collection of products with low stock
     */
    public function getLowStockProducts(int $limit = 10): Collection
    {
        return Product::select('products.*')
            ->selectRaw('COALESCE(SUM(stock_balances.quantity), 0) as total_stock')
            ->leftJoin('stock_balances', 'products.id', '=', 'stock_balances.product_id')
            ->groupBy('products.id')
            ->havingRaw('total_stock <= products.min_stock_level')
            ->orderByRaw('total_stock ASC')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get products that are out of stock
     *
     * @param int $limit Maximum number of products to return
     *
     * @return Collection Collection of out-of-stock products
     */
    public function getOutOfStockProducts(int $limit = 10): Collection
    {
        return Product::select('products.*')
            ->selectRaw('COALESCE(SUM(stock_balances.quantity), 0) as total_stock')
            ->leftJoin('stock_balances', 'products.id', '=', 'stock_balances.product_id')
            ->groupBy('products.id')
            ->havingRaw('total_stock <= 0')
            ->orderBy('products.name')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Calculate total stock value
     *
     * Calculates the total monetary value of all stock.
     *
     * @return float Total stock value in currency
     */
    public function calculateTotalStockValue(): float
    {
        return StockBalance::join('products', 'stock_balances.product_id', '=', 'products.id')
            ->sum(DB::raw('stock_balances.quantity * products.purchase_price'));
    }
    
    /**
     * Get stock movement trends
     *
     * @param int $days Number of days to analyze (default: 30)
     *
     * @return Collection Collection of stock movement data
     */
    public function getStockMovementTrends(int $days = 30): Collection
    {
        $startDate = now()->subDays($days);
        
        return StockTransaction::select(
            DB::raw('DATE(created_at) as date'),
            'type',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('COUNT(*) as transaction_count')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'), 'type')
            ->orderBy('date')
            ->get();
    }
}