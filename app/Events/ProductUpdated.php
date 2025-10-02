<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Product $product;
    public array $changes;
    public array $original;

    /**
     * Create a new event instance.
     *
     * @param Product $product
     * @param array $changes
     * @param array $original
     */
    public function __construct(Product $product, array $changes, array $original)
    {
        $this->product = $product;
        $this->changes = $changes;
        $this->original = $original;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array
     */
    public function broadcastOn(): array
    {
        return [];
    }
}