<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductDataSynced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $changes;
    public $syncResults;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     * @param array $changes
     * @param array $syncResults
     */
    public function __construct(Product $product, array $changes, array $syncResults)
    {
        $this->product = $product;
        $this->changes = $changes;
        $this->syncResults = $syncResults;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('product.' . $this->product->id),
            new Channel('products.list'),
            new Channel('inventory.updates'),
            new Channel('dashboard.stats'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
                'price' => $this->product->price,
                'stock' => $this->product->stock,
                'expiration_date' => $this->product->expiration_date,
                'updated_at' => $this->product->updated_at,
            ],
            'changes' => $this->changes,
            'sync_results' => $this->syncResults,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'product.data.synced';
    }
}