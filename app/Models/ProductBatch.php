<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_no',
        'mfg_date',
        'exp_date',
        'mrp',
        'purchase_price',
        'barcode',
    ];

    protected $casts = [
        'mfg_date' => 'date',
        'exp_date' => 'date',
        'mrp' => 'decimal:2',
        'purchase_price' => 'decimal:2',
    ];

    /**
     * Get the product that owns the batch.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the stock balances for the batch.
     */
    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class, 'batch_id');
    }

    /**
     * Get the stock transactions for the batch.
     */
    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class, 'batch_id');
    }

    /**
     * Check if batch is expired.
     */
    public function getIsExpiredAttribute()
    {
        return $this->exp_date && $this->exp_date->isPast();
    }

    /**
     * Check if batch is near expiry (within 30 days).
     */
    public function getIsNearExpiryAttribute()
    {
        return $this->exp_date && $this->exp_date->diffInDays(now()) <= 30 && !$this->is_expired;
    }
}