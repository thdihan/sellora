<?php

/**
 * Product Model
 *
 * Represents a product in the inventory system with relationships to categories,
 * brands, units, stock balances, and pricing information.
 *
 * @category Model
 * @package  App\Models
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0
 * @link     https://sellora.com
 * @since    2024
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Product Model Class
 *
 * Manages product data including inventory, pricing, categorization,
 * and relationships with other entities in the system.
 *
 * @category Models
 * @package  App\Models
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0
 * @link     https://sellora.com
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'selling_price',
        'purchase_price',
        'stock',
        'expiration_date',
        'category_id',
        'brand_id',
        'unit_id',
        'warehouse_id',
        
        'reorder_level',
        'barcode',
        'weight',
        'dimensions',
        'status',
        'tax_code',
        'tax_rate',
        'is_taxable',
        'meta_data',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
        'expiration_date' => 'date',
        'weight' => 'decimal:2',
        'status' => 'boolean',
        'tax_rate' => 'decimal:4',
        'is_taxable' => 'boolean',
        'meta_data' => 'array',
    ];

    /**
     * Get the category that owns the product.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the brand that owns the product.
     *
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    /**
     * Get the unit that owns the product.
     *
     * @return BelongsTo
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    /**
     * Get the warehouse that owns the product.
     *
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }



    /**
     * Get the batches for the product.
     *
     * @return HasMany
     */
    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    /**
     * Get the stock balances for the product.
     *
     * @return HasMany
     */
    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    /**
     * Get the stock transactions for the product.
     *
     * @return HasMany
     */
    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }



    /**
     * Get the files for the product.
     *
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(ProductFile::class);
    }

    /**
     * Scope a query to only include active products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get total stock quantity across all warehouses.
     *
     * @return int
     */
    public function getTotalStockAttribute()
    {
        return $this->stockBalances()->sum('qty_on_hand');
    }

    /**
     * Get available stock (on hand minus reserved).
     *
     * @return int
     */
    public function getAvailableStockAttribute()
    {
        return $this->stockBalances()->selectRaw('SUM(qty_on_hand - qty_reserved) as available')->value('available') ?? 0;
    }

    /**
     * Get the product prices for the product.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    /**
     * Get the media files for the product.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get the external product mappings.
     */
    public function externalMappings(): HasMany
    {
        return $this->hasMany(ExternalProductMap::class);
    }

    /**
     * Get the sync logs for the product.
     */
    public function syncLogs(): MorphMany
    {
        return $this->morphMany(SyncLog::class, 'syncable');
    }

    /**
     * Get active product prices.
     */
    public function activePrices(): HasMany
    {
        return $this->prices()->where('is_active', true);
    }

    /**
     * Get primary media (images).
     */
    public function images(): MorphMany
    {
        return $this->media()->where('collection', 'images');
    }

    /**
     * Get primary product image.
     */
    public function primaryImage(): MorphMany
    {
        return $this->images()->where('is_primary', true);
    }

    /**
     * Get price for specific customer and quantity.
     */
    public function getPriceForCustomer($customerId = null, $customerGroupId = null, $quantity = 1)
    {
        $query = $this->activePrices()
            ->where('min_quantity', '<=', $quantity)
            ->orderBy('min_quantity', 'desc');

        if ($customerId) {
            $query->where(function ($q) use ($customerId) {
                $q->where('customer_id', $customerId)
                  ->orWhereNull('customer_id');
            });
        }

        if ($customerGroupId) {
            $query->where(function ($q) use ($customerGroupId) {
                $q->where('customer_group_id', $customerGroupId)
                  ->orWhereNull('customer_group_id');
            });
        }

        $price = $query->first();
        
        return $price ? $price->price : $this->selling_price;
    }

    /**
     * Calculate tax amount for the product.
     */
    public function calculateTax($baseAmount = null)
    {
        if (!$this->is_taxable || !$this->tax_rate) {
            return 0;
        }

        $amount = $baseAmount ?? $this->selling_price;
        return $amount * ($this->tax_rate / 100);
    }

    /**
     * Get price including tax.
     */
    public function getPriceIncludingTax($customerId = null, $customerGroupId = null, $quantity = 1)
    {
        $basePrice = $this->getPriceForCustomer($customerId, $customerGroupId, $quantity);
        $tax = $this->calculateTax($basePrice);
        
        return $basePrice + $tax;
    }

    /**
     * Check if product needs reorder.
     */
    public function needsReorder(): bool
    {
        return $this->total_stock <= $this->reorder_level;
    }

    /**
     * Scope for products that need reorder.
     */
    public function scopeNeedsReorder($query)
    {
        return $query->whereRaw('(SELECT SUM(qty_on_hand) FROM stock_balances WHERE product_id = products.id) <= reorder_level');
    }

    /**
     * Scope for taxable products.
     */
    public function scopeTaxable($query)
    {
        return $query->where('is_taxable', true);
    }

    /**
     * Get the price attribute (alias for selling_price for backward compatibility).
     */
    public function getPriceAttribute()
    {
        return $this->selling_price;
    }

    /**
     * Set the price attribute (sets selling_price for backward compatibility).
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['selling_price'] = $value;
    }
}