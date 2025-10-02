<?php

/**
 * Stock Transaction Model
 *
 * @package App\Models
 * @author  Sellora Team
 * @version 1.0.0
 * @license MIT
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class StockTransaction
 *
 * @package App\Models
 */
class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'batch_id',
        'qty',
        'type',
        'ref_type',
        'ref_id',
        'note',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    const TYPE_OPENING = 'opening';
    const TYPE_PURCHASE_IN = 'purchase_in';
    const TYPE_INBOUND = 'inbound';
    const TYPE_OUTBOUND = 'outbound';
    const TYPE_TRANSFER_IN = 'transfer_in';
    const TYPE_TRANSFER_OUT = 'transfer_out';
    const TYPE_SALE_RESERVE = 'sale_reserve';
    const TYPE_RELEASE_RESERVE = 'release_reserve';
    const TYPE_SALE_DISPATCH = 'sale_dispatch';
    const TYPE_SALE_RETURN = 'sale_return';
    const TYPE_ADJUSTMENT_IN = 'adjustment_in';
    const TYPE_ADJUSTMENT_OUT = 'adjustment_out';

    /**
     * Get the product that owns the stock transaction.
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse that owns the stock transaction.
     *
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the batch that owns the transaction.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }

    /**
     * Check if transaction increases stock.
     */
    public function getIsInboundAttribute()
    {
        return in_array($this->type, [
            self::TYPE_OPENING,
            self::TYPE_PURCHASE_IN,
            self::TYPE_TRANSFER_IN,
            self::TYPE_RELEASE_RESERVE,
            self::TYPE_SALE_RETURN,
            self::TYPE_ADJUSTMENT_IN,
        ]);
    }

    /**
     * Scope a query to only include inbound transactions.
     *
     * @param $query
     * @return mixed
     */
    public function scopeInbound($query)
    {
        return $query->whereIn(
            'type',
            [
                self::TYPE_OPENING,
                self::TYPE_PURCHASE_IN,
                self::TYPE_TRANSFER_IN,
                self::TYPE_RELEASE_RESERVE,
                self::TYPE_SALE_RETURN,
                self::TYPE_ADJUSTMENT_IN,
            ]
        );
    }

    /**
     * Scope a query to only include outbound transactions.
     *
     * @param $query
     * @return mixed
     */
    public function scopeOutbound($query)
    {
        return $query->whereIn(
            'type',
            [
                self::TYPE_TRANSFER_OUT,
                self::TYPE_SALE_RESERVE,
                self::TYPE_SALE_DISPATCH,
                self::TYPE_ADJUSTMENT_OUT,
            ]
        );
    }
}