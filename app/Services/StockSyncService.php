<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class StockSyncService
{
    /**
     * Sync all product stock with stock balances.
     *
     * @return array
     */
    public function syncAllProducts(): array
    {
        $results = [
            'synced' => 0,
            'errors' => 0,
            'skipped' => 0,
            'details' => []
        ];

        $products = Product::all();
        $mainWarehouse = Warehouse::where('is_main', true)->first();

        if (!$mainWarehouse) {
            throw new \Exception('Main warehouse not found');
        }

        foreach ($products as $product) {
            try {
                $syncResult = $this->syncProductStock($product, $mainWarehouse);
                if ($syncResult['synced']) {
                    $results['synced']++;
                } else {
                    $results['skipped']++;
                }
                $results['details'][] = $syncResult;
            } catch (\Exception $e) {
                $results['errors']++;
                $results['details'][] = [
                    'product_id' => $product->id,
                    'product_sku' => $product->sku,
                    'synced' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Sync individual product stock with stock balance.
     *
     * @param Product $product
     * @param Warehouse|null $warehouse
     * @return array
     */
    public function syncProductStock(Product $product, ?Warehouse $warehouse = null): array
    {
        if (!$warehouse) {
            $warehouse = Warehouse::where('is_main', true)->first();
            if (!$warehouse) {
                throw new \Exception('Main warehouse not found');
            }
        }

        $currentBalance = StockBalance::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        $productStock = $product->stock ?? 0;
        $balanceStock = $currentBalance ? $currentBalance->qty_on_hand : 0;
        $difference = $productStock - $balanceStock;

        if ($difference == 0) {
            return [
                'product_id' => $product->id,
                'product_sku' => $product->sku,
                'synced' => false,
                'reason' => 'No difference found',
                'product_stock' => $productStock,
                'balance_stock' => $balanceStock
            ];
        }

        DB::beginTransaction();
        try {
            // Update or create stock balance
            StockBalance::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id
                ],
                [
                    'qty_on_hand' => $productStock
                ]
            );

            // Create stock transaction for audit trail
            StockTransaction::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'type' => $difference > 0 ? 'adjustment_in' : 'adjustment_out',
                'qty' => abs($difference),
                'reference' => 'Stock sync service',
                'reason' => 'Automatic synchronization between product and stock balance',
                'user_id' => Auth::id()
            ]);

            DB::commit();

            Log::info('Product stock synchronized', [
                'product_id' => $product->id,
                'product_sku' => $product->sku,
                'difference' => $difference,
                'new_balance' => $productStock
            ]);

            return [
                'product_id' => $product->id,
                'product_sku' => $product->sku,
                'synced' => true,
                'difference' => $difference,
                'product_stock' => $productStock,
                'balance_stock' => $balanceStock,
                'new_balance' => $productStock
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get products with stock discrepancies.
     *
     * @return Collection
     */
    public function getStockDiscrepancies(): Collection
    {
        $mainWarehouse = Warehouse::where('is_main', true)->first();
        if (!$mainWarehouse) {
            return collect([]);
        }

        return Product::select('products.*')
            ->leftJoin('stock_balances', function ($join) use ($mainWarehouse) {
                $join->on('products.id', '=', 'stock_balances.product_id')
                     ->where('stock_balances.warehouse_id', $mainWarehouse->id);
            })
            ->whereRaw('COALESCE(products.stock, 0) != COALESCE(stock_balances.quantity, 0)')
            ->with(['stockBalances' => function ($query) use ($mainWarehouse) {
                $query->where('warehouse_id', $mainWarehouse->id);
            }])
            ->get()
            ->map(function ($product) {
                $balance = $product->stockBalances->first();
                return [
                    'product' => $product,
                    'product_stock' => $product->stock ?? 0,
                    'balance_stock' => $balance ? $balance->quantity : 0,
                    'difference' => ($product->stock ?? 0) - ($balance ? $balance->quantity : 0)
                ];
            });
    }

    /**
     * Recalculate stock balance from transactions.
     *
     * @param Product $product
     * @param Warehouse|null $warehouse
     * @return array
     */
    public function recalculateFromTransactions(Product $product, ?Warehouse $warehouse = null): array
    {
        if (!$warehouse) {
            $warehouse = Warehouse::where('is_main', true)->first();
            if (!$warehouse) {
                throw new \Exception('Main warehouse not found');
            }
        }

        $transactions = StockTransaction::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->orderBy('created_at')
            ->get();

        $calculatedBalance = 0;
        foreach ($transactions as $transaction) {
            if (in_array($transaction->type, ['purchase', 'adjustment_in', 'transfer_in'])) {
                $calculatedBalance += $transaction->quantity;
            } elseif (in_array($transaction->type, ['sale', 'adjustment_out', 'transfer_out'])) {
                $calculatedBalance -= $transaction->quantity;
            }
        }

        $currentBalance = StockBalance::where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->first();

        $currentQuantity = $currentBalance ? $currentBalance->quantity : 0;
        $difference = $calculatedBalance - $currentQuantity;

        if ($difference != 0) {
            DB::beginTransaction();
            try {
                StockBalance::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id
                    ],
                    [
                        'qty_on_hand' => $calculatedBalance
                    ]
                );

                // Update product stock to match calculated balance
                $product->update(['stock' => $calculatedBalance]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }

        return [
            'product_id' => $product->id,
            'calculated_balance' => $calculatedBalance,
            'previous_balance' => $currentQuantity,
            'difference' => $difference,
            'updated' => $difference != 0
        ];
    }
}