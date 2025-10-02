<?php

/**
 * Stock Transaction Service
 *
 * This service handles all stock transaction operations including adjustments,
 * transfers, inbound/outbound transactions, and stock balance management.
 *
 * @package App\Services
 * @author  Sellora Team <team@sellora.com>
 * @version 1.0.0
 * @category Services
 * @license  MIT
 * @link     https://sellora.com
 */

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Class StockTransactionService
 *
 * Manages stock transactions and inventory operations across warehouses.
 *
 * @package App\Services
 * @author  Sellora Team <team@sellora.com>
 * @category Services
 * @license  MIT
 * @link     https://sellora.com
 */
class StockTransactionService
{
    /**
     * Create a stock adjustment transaction
     *
     * @param array $data The adjustment data containing product_id, warehouse_id,
     *                    new_quantity, batch_id (optional), notes (optional),
     *                    and unit_cost (optional)
     *
     * @return StockTransaction
     * @throws Exception When no adjustment is needed or validation fails
     */
    public function createAdjustment(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            
            // Ensure warehouse_id is provided, fallback to main warehouse
            if (!isset($data['warehouse_id']) || empty($data['warehouse_id'])) {
                $mainWarehouse = Warehouse::getMain();
                if (!$mainWarehouse) {
                    throw new Exception('No warehouse specified and main warehouse not found.');
                }
                $data['warehouse_id'] = $mainWarehouse->id;
            }
            
            $warehouse = Warehouse::findOrFail($data['warehouse_id']);
            $batch = null;
            
            if (isset($data['batch_id'])) {
                $batch = ProductBatch::findOrFail($data['batch_id']);
            }
            
            // Determine transaction type based on adjustment
            $currentStock = $this->getCurrentStock($product->id, $warehouse->id, $batch?->id);
            $newQuantity = $data['new_quantity'];
            $difference = $newQuantity - $currentStock;
            
            if ($difference == 0) {
                throw new Exception('No adjustment needed. Current stock matches new quantity.');
            }
            
            $type = $difference > 0 ? 'inbound' : 'outbound';
            $quantity = abs($difference);
            
            // Create transaction
            $transaction = StockTransaction::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'batch_id' => $batch?->id,
                'type' => $type,
                'quantity' => $quantity,
                'reference_type' => 'adjustment',
                'reference_id' => null,
                'notes' => $data['notes'] ?? 'Stock adjustment',
                'unit_cost' => $data['unit_cost'] ?? $product->purchase_price,
                'total_cost' => $quantity * ($data['unit_cost'] ?? $product->purchase_price),
            ]);
            
            // Update stock balance
            $this->updateStockBalance($transaction);
            
            return $transaction;
        });
    }
    
    /**
     * Create a stock transfer between warehouses
     *
     * @param array $data The transfer data containing product_id, from_warehouse_id,
     *                    to_warehouse_id, quantity, batch_id (optional),
     *                    notes (optional), and unit_cost (optional)
     *
     * @return array Array containing 'outbound' and 'inbound' transactions
     * @throws Exception When warehouses are same or insufficient stock
     */
    public function createTransfer(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            $fromWarehouse = Warehouse::findOrFail($data['from_warehouse_id']);
            $toWarehouse = Warehouse::findOrFail($data['to_warehouse_id']);
            $batch = null;
            
            if (isset($data['batch_id'])) {
                $batch = ProductBatch::findOrFail($data['batch_id']);
            }
            
            if ($fromWarehouse->id === $toWarehouse->id) {
                throw new Exception('Cannot transfer to the same warehouse.');
            }
            
            // Check available stock
            $availableStock = $this->getAvailableStock($product->id, $fromWarehouse->id, $batch?->id);
            if ($availableStock < $data['quantity']) {
                throw new Exception("Insufficient stock. Available: {$availableStock}, Requested: {$data['quantity']}");
            }
            
            $unitCost = $data['unit_cost'] ?? $product->purchase_price;
            $totalCost = $data['quantity'] * $unitCost;
            
            // Create outbound transaction (from source warehouse)
            $outboundTransaction = StockTransaction::create([
                'product_id' => $product->id,
                'warehouse_id' => $fromWarehouse->id,
                'batch_id' => $batch?->id,
                'type' => 'outbound',
                'quantity' => $data['quantity'],
                'reference_type' => 'transfer',
                'reference_id' => null, // Will be updated with inbound transaction ID
                'notes' => $data['notes'] ?? "Transfer to {$toWarehouse->name}",
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ]);
            
            // Create inbound transaction (to destination warehouse)
            $inboundTransaction = StockTransaction::create([
                'product_id' => $product->id,
                'warehouse_id' => $toWarehouse->id,
                'batch_id' => $batch?->id,
                'type' => 'inbound',
                'quantity' => $data['quantity'],
                'reference_type' => 'transfer',
                'reference_id' => $outboundTransaction->id,
                'notes' => $data['notes'] ?? "Transfer from {$fromWarehouse->name}",
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ]);
            
            // Update reference for outbound transaction
            $outboundTransaction->update(['reference_id' => $inboundTransaction->id]);
            
            // Update stock balances
            $this->updateStockBalance($outboundTransaction);
            $this->updateStockBalance($inboundTransaction);
            
            return [
                'outbound' => $outboundTransaction,
                'inbound' => $inboundTransaction,
            ];
        });
    }
    
    /**
     * Create an inbound stock transaction (purchase, production, etc.)
     *
     * @param array $data The inbound data containing product_id, warehouse_id,
     *                    quantity, batch_number/batch_id (optional),
     *                    manufacturing_date (optional), expiry_date (optional),
     *                    reference_type (optional), reference_id (optional), 
     *                    notes (optional), and unit_cost (optional)
     *
     * @return StockTransaction
     */
    public function createInbound(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            $warehouse = Warehouse::findOrFail($data['warehouse_id']);
            $batch = null;
            
            // Create or find batch if batch data provided
            if (isset($data['batch_number']) || isset($data['batch_id'])) {
                if (isset($data['batch_id'])) {
                    $batch = ProductBatch::findOrFail($data['batch_id']);
                } else {
                    $batch = ProductBatch::firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'batch_number' => $data['batch_number'],
                        ],
                        [
                            'manufacturing_date' => $data['manufacturing_date'] ?? null,
                            'expiry_date' => $data['expiry_date'] ?? null,
                        ]
                    );
                }
            }
            
            $unitCost = $data['unit_cost'] ?? $product->purchase_price;
            $totalCost = $data['quantity'] * $unitCost;
            
            // Create transaction
            $transaction = StockTransaction::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'batch_id' => $batch?->id,
                'type' => 'inbound',
                'quantity' => $data['quantity'],
                'reference_type' => $data['reference_type'] ?? 'manual',
                'reference_id' => $data['reference_id'] ?? null,
                'notes' => $data['notes'] ?? 'Stock inbound',
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ]);
            
            // Update stock balance
            $this->updateStockBalance($transaction);
            
            return $transaction;
        });
    }
    
    /**
     * Create an outbound stock transaction (sale, consumption, etc.)
     *
     * @param array $data The outbound data containing product_id, warehouse_id,
     *                    quantity, batch_id (optional), reference_type (optional),
     *                    reference_id (optional), notes (optional),
     *                    and unit_cost (optional)
     *
     * @return StockTransaction
     * @throws Exception When insufficient stock is available
     */
    public function createOutbound(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            $warehouse = Warehouse::findOrFail($data['warehouse_id']);
            $batch = null;
            
            if (isset($data['batch_id'])) {
                $batch = ProductBatch::findOrFail($data['batch_id']);
            }
            
            // Check available stock
            $availableStock = $this->getAvailableStock($product->id, $warehouse->id, $batch?->id);
            if ($availableStock < $data['quantity']) {
                throw new Exception("Insufficient stock. Available: {$availableStock}, Requested: {$data['quantity']}");
            }
            
            $unitCost = $data['unit_cost'] ?? $product->purchase_price;
            $totalCost = $data['quantity'] * $unitCost;
            
            // Create transaction
            $transaction = StockTransaction::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'batch_id' => $batch?->id,
                'type' => 'outbound',
                'quantity' => $data['quantity'],
                'reference_type' => $data['reference_type'] ?? 'manual',
                'reference_id' => $data['reference_id'] ?? null,
                'notes' => $data['notes'] ?? 'Stock outbound',
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
            ]);
            
            // Update stock balance
            $this->updateStockBalance($transaction);
            
            return $transaction;
        });
    }
    
    /**
     * Get current stock for a product in a warehouse
     *
     * @param int      $productId   The product ID
     * @param int      $warehouseId The warehouse ID
     * @param int|null $batchId     The batch ID (optional)
     *
     * @return int The current stock quantity
     */
    public function getCurrentStock(int $productId, int $warehouseId, ?int $batchId = null): int
    {
        $query = StockBalance::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId);
            
        if ($batchId) {
            $query->where('batch_id', $batchId);
        } else {
            $query->whereNull('batch_id');
        }
        
        return $query->sum('quantity');
    }
    
    /**
     * Get available stock (excluding reserved) for a product in a warehouse
     *
     * @param int      $productId   The product ID
     * @param int      $warehouseId The warehouse ID
     * @param int|null $batchId     The batch ID (optional)
     *
     * @return int The available stock quantity
     */
    public function getAvailableStock(int $productId, int $warehouseId, ?int $batchId = null): int
    {
        $query = StockBalance::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId);
            
        if ($batchId) {
            $query->where('batch_id', $batchId);
        } else {
            $query->whereNull('batch_id');
        }
        
        return $query->sum('available_quantity');
    }
    
    /**
     * Update stock balance based on transaction
     *
     * @param StockTransaction $transaction The stock transaction to process
     *
     * @return void
     */
    protected function updateStockBalance(StockTransaction $transaction): void
    {
        $stockBalance = StockBalance::firstOrCreate(
            [
                'product_id' => $transaction->product_id,
                'warehouse_id' => $transaction->warehouse_id,
                'batch_id' => $transaction->batch_id,
            ],
            [
                'quantity' => 0,
                'reserved_quantity' => 0,
            ]
        );
        
        if ($transaction->type === 'inbound') {
            $stockBalance->increment('quantity', $transaction->quantity);
        } else {
            $stockBalance->decrement('quantity', $transaction->quantity);
        }
    }
    
    /**
     * Reserve stock for a specific purpose
     *
     * @param int      $productId   The product ID
     * @param int      $warehouseId The warehouse ID
     * @param int      $quantity    The quantity to reserve
     * @param int|null $batchId     The batch ID (optional)
     *
     * @return bool True if reservation successful
     * @throws Exception When insufficient available stock for reservation
     */
    public function reserveStock(int $productId, int $warehouseId, int $quantity, ?int $batchId = null): bool
    {
        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $batchId) {
            $stockBalance = StockBalance::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->where('batch_id', $batchId)
                ->lockForUpdate()
                ->first();
                
            if (!$stockBalance || $stockBalance->available_quantity < $quantity) {
                throw new Exception('Insufficient available stock for reservation.');
            }
            
            $stockBalance->increment('reserved_quantity', $quantity);
            
            return true;
        });
    }
    
    /**
     * Release reserved stock
     *
     * @param int      $productId   The product ID
     * @param int      $warehouseId The warehouse ID
     * @param int      $quantity    The quantity to release
     * @param int|null $batchId     The batch ID (optional)
     *
     * @return bool True if release successful
     */
    public function releaseReservedStock(int $productId, int $warehouseId, int $quantity, ?int $batchId = null): bool
    {
        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $batchId) {
            $stockBalance = StockBalance::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->where('batch_id', $batchId)
                ->lockForUpdate()
                ->first();
                
            if ($stockBalance) {
                $stockBalance->decrement('reserved_quantity', min($quantity, $stockBalance->reserved_quantity));
            }
            
            return true;
        });
    }
}