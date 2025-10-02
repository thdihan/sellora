<?php

/**
 * Stock Reservation Service
 *
 * Handles stock reservation and release operations for products
 *
 * @category Services
 * @package  App\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Services;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\StockBalance;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * Class StockReservationService
 *
 * Manages stock reservations for order processing
 *
 * @category Services
 * @package  App\Services
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class StockReservationService
{
    /**
     * Reserve stock for a product
     *
     * @param int $productId   The product ID to reserve stock for
     * @param int $quantity    The quantity to reserve
     * @param int $warehouseId The warehouse ID (optional)
     *
     * @return array Array of reservation details
     *
     * @throws Exception When insufficient stock is available
     */
    public function reserveStock(int $productId, int $quantity, int $warehouseId = 1): array
    {
        return DB::transaction(
            function () use ($productId, $quantity, $warehouseId) {
                // Get current stock balance
                $stockBalance = StockBalance::firstOrCreate(
                    [
                        'product_id' => $productId,
                        'warehouse_id' => $warehouseId,
                        'batch_id' => null
                    ],
                    [
                        'qty_on_hand' => 0,
                        'qty_reserved' => 0
                    ]
                );

                $availableStock = $stockBalance->qty_on_hand - $stockBalance->qty_reserved;

                if ($availableStock < $quantity) {
                    throw new Exception(
                        "Insufficient stock available. Required: {$quantity}, Available: {$availableStock}"
                    );
                }

                // Create stock transaction
                $transaction = StockTransaction::create(
                    [
                        'product_id' => $productId,
                        'warehouse_id' => $warehouseId,
                        'batch_id' => null,
                        'qty' => $quantity,
                        'type' => 'sale_reserve',
                        'ref_type' => 'order',
                        'note' => "Stock reserved for order"
                    ]
                );

                // Update stock balance
                $stockBalance->increment('qty_reserved', $quantity);

                return [
                    [
                        'product_id' => $productId,
                        'quantity_reserved' => $quantity,
                        'warehouse_id' => $warehouseId,
                        'transaction_id' => $transaction->id
                    ]
                ];
            }
        );
    }

    /**
     * Release reserved stock
     *
     * @param array $reservations Array of reservation details to release
     *
     * @return bool Success status
     */
    public function releaseStock(array $reservations): bool
    {
        return DB::transaction(
            function () use ($reservations) {
                foreach ($reservations as $reservation) {
                    // Find the transaction
                    $transaction = StockTransaction::find($reservation['transaction_id']);
                    if (!$transaction) {
                        continue;
                    }

                    // Update stock balance
                    $stockBalance = StockBalance::where(
                        [
                            'product_id' => $transaction->product_id,
                            'warehouse_id' => $transaction->warehouse_id,
                            'batch_id' => null
                        ]
                    )->first();

                    if ($stockBalance) {
                        $stockBalance->decrement('qty_reserved', $reservation['quantity_reserved']);
                    }

                    // Create release transaction
                    StockTransaction::create(
                        [
                            'product_id' => $transaction->product_id,
                            'warehouse_id' => $transaction->warehouse_id,
                            'batch_id' => null,
                            'qty' => $reservation['quantity_reserved'],
                            'type' => 'release_reserve',
                            'ref_type' => 'order',
                            'note' => 'Stock reservation released'
                        ]
                    );

                    // Delete the original transaction
                    $transaction->delete();
                }

                return true;
            }
        );
    }

    /**
     * Get available stock for a product
     *
     * @param int $productId   The product ID to check stock for
     * @param int $warehouseId The warehouse ID
     *
     * @return int Available stock quantity
     */
    public function getAvailableStock(int $productId, int $warehouseId = 1): int
    {
        $stockBalance = StockBalance::where(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'batch_id' => null
            ]
        )->first();

        if (!$stockBalance) {
            return 0;
        }

        return max(0, $stockBalance->qty_on_hand - $stockBalance->qty_reserved);
    }

    /**
     * Check if sufficient stock is available
     *
     * @param int $productId   The product ID to check
     * @param int $quantity    The required quantity
     * @param int $warehouseId The warehouse ID
     *
     * @return bool True if sufficient stock is available
     */
    public function hasAvailableStock(int $productId, int $quantity, int $warehouseId = 1): bool
    {
        return $this->getAvailableStock($productId, $warehouseId) >= $quantity;
    }

    /**
     * Get stock details for a product
     *
     * @param int $productId   The product ID
     * @param int $warehouseId The warehouse ID
     *
     * @return array Stock details including on hand and reserved quantities
     */
    public function getStockDetails(int $productId, int $warehouseId = 1): array
    {
        $balance = StockBalance::where(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'batch_id' => null
            ]
        )->first();

        return [
            'on_hand' => $balance->qty_on_hand ?? 0,
            'reserved' => $balance->qty_reserved ?? 0,
            'available' => $this->getAvailableStock($productId, $warehouseId)
        ];
    }
}