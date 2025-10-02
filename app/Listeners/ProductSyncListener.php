<?php

namespace App\Listeners;

use App\Events\ProductUpdated;
use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductSyncListener
{
    /**
     * Handle the event.
     *
     * @param ProductUpdated $event
     * @return void
     */
    public function handle(ProductUpdated $event): void
    {
        $product = $event->product;
        $changes = $event->changes;
        $original = $event->original;

        // Handle stock synchronization
        if (isset($changes['stock'])) {
            $this->syncStockBalance($product, $changes['stock'], $original['stock'] ?? 0);
        }

        // Handle price synchronization
        if (isset($changes['price'])) {
            $this->logPriceChange($product, $changes['price'], $original['price'] ?? 0);
        }

        // Log the synchronization
        Log::info('Product synchronized', [
            'product_id' => $product->id,
            'changes' => $changes,
            'user_id' => Auth::id()
        ]);
    }

    /**
     * Synchronize stock balance with product stock.
     *
     * @param mixed $product
     * @param mixed $newStock
     * @param mixed $oldStock
     * @return void
     */
    private function syncStockBalance($product, $newStock, $oldStock): void
    {
        $mainWarehouse = Warehouse::where('is_main', true)->first();
        if (!$mainWarehouse) {
            return;
        }

        $difference = $newStock - $oldStock;
        if ($difference == 0) {
            return;
        }

        // Update stock balance
        StockBalance::updateOrCreate(
            [
                'product_id' => $product->id,
                'warehouse_id' => $mainWarehouse->id
            ],
            [
                'qty_on_hand' => $newStock
            ]
        );

        // Create stock transaction
        StockTransaction::create([
            'product_id' => $product->id,
            'warehouse_id' => $mainWarehouse->id,
            'type' => $difference > 0 ? 'adjustment_in' : 'adjustment_out',
            'qty' => abs($difference),
            'reference' => 'Product sync',
            'reason' => 'Automatic sync from product update',
            'user_id' => Auth::id()
        ]);
    }

    /**
     * Log price changes.
     *
     * @param mixed $product
     * @param mixed $newPrice
     * @param mixed $oldPrice
     * @return void
     */
    private function logPriceChange($product, $newPrice, $oldPrice): void
    {
        Log::info('Product price changed', [
            'product_id' => $product->id,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'user_id' => Auth::id()
        ]);
    }
}