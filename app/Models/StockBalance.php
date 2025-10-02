<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'batch_id',
        'qty_on_hand',
        'qty_reserved',
    ];

    protected $casts = [
        'qty_on_hand' => 'integer',
        'qty_reserved' => 'integer',
    ];

    /**
     * Get the product that owns the stock balance.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse that owns the stock balance.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the batch that owns the stock balance.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }

    /**
     * Get available quantity (on hand minus reserved).
     */
    public function getAvailableQtyAttribute()
    {
        return $this->qty_on_hand - $this->qty_reserved;
    }

    /**
     * Scope a query to only include records with stock.
     */
    public function scopeWithStock($query)
    {
        return $query->where('qty_on_hand', '>', 0);
    }

    /**
     * Scope a query to only include records with available stock.
     */
    public function scopeWithAvailableStock($query)
    {
        return $query->whereRaw('qty_on_hand > qty_reserved');
    }
}