<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTaxLine extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'tax_head_id',
        'base_amount',
        'rate',
        'calculated_amount',
        'payer',
        'visible',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'calculated_amount' => 'decimal:2',
        'visible' => 'boolean',
    ];

    /**
     * Get the order that owns this tax line.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the tax head for this tax line.
     */
    public function taxHead(): BelongsTo
    {
        return $this->belongsTo(TaxHead::class);
    }

    /**
     * Scope a query to only include visible tax lines.
     */
    public function scopeVisible($query)
    {
        return $query->where('visible', true);
    }

    /**
     * Scope a query to filter by payer.
     */
    public function scopeByPayer($query, $payer)
    {
        return $query->where('payer', $payer);
    }
}
