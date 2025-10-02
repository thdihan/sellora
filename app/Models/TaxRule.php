<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class TaxRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'tax_rate_id',
        'applies_to',
        'category_id',
        'product_id',
        'price_mode',
        'bearer',
        'reverse_charge',
        'zero_rated',
        'exempt',
        'withholding',
        'withholding_percent',
        'taxable_discounts',
        'taxable_shipping',
        'place_of_supply',
        'rounding',
        'priority',
        'is_active',
        'comments',
    ];

    protected $casts = [
        'reverse_charge' => 'boolean',
        'zero_rated' => 'boolean',
        'exempt' => 'boolean',
        'withholding' => 'boolean',
        'withholding_percent' => 'decimal:2',
        'taxable_shipping' => 'boolean',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tax rate that owns this tax rule.
     */
    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    /**
     * Get the product category if this rule applies to a category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the product if this rule applies to a specific product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include active tax rules.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by priority.
     */
    public function scopeByPriority(Builder $query): Builder
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Check if this rule applies to a given product.
     */
    public function appliesTo(Product $product): bool
    {
        if (!$this->is_active) {
            return false;
        }

        switch ($this->applies_to) {
            case 'all':
                return true;
            case 'category':
                return $this->category_id === $product->category_id;
            case 'product':
                return $this->product_id === $product->id;
            case 'shipping':
            case 'fees':
                return false; // These are handled separately
            default:
                return false;
        }
    }

    /**
     * Calculate tax for a given amount based on this rule.
     */
    public function calculateTax(float $amount, array $options = []): array
    {
        $result = [
            'base_amount' => $amount,
            'tax_amount' => 0,
            'total_amount' => $amount,
            'tax_rate' => $this->taxRate->percent ?? 0,
            'rule_applied' => $this->name,
            'is_exempt' => $this->exempt,
            'is_zero_rated' => $this->zero_rated,
            'reverse_charge' => $this->reverse_charge,
        ];

        // Handle exempt or zero-rated items
        if ($this->exempt || $this->zero_rated) {
            return $result;
        }

        // Calculate base tax amount
        $taxRate = $this->taxRate->percent / 100;
        
        if ($this->price_mode === 'INCLUSIVE') {
            // Tax is included in the price
            $result['base_amount'] = $amount / (1 + $taxRate);
            $result['tax_amount'] = $amount - $result['base_amount'];
        } else {
            // Tax is added to the price
            $result['tax_amount'] = $amount * $taxRate;
        }

        // Apply withholding tax if applicable
        if ($this->withholding && $this->withholding_percent > 0) {
            $withholdingAmount = $result['tax_amount'] * ($this->withholding_percent / 100);
            $result['withholding_amount'] = $withholdingAmount;
            $result['tax_amount'] -= $withholdingAmount;
        }

        // Calculate total
        if ($this->price_mode === 'INCLUSIVE') {
            $result['total_amount'] = $amount;
        } else {
            $result['total_amount'] = $result['base_amount'] + $result['tax_amount'];
        }

        // Round amounts based on rounding rule
        $result = $this->applyRounding($result);

        return $result;
    }

    /**
     * Apply rounding based on the rule's rounding setting.
     */
    private function applyRounding(array $result): array
    {
        switch ($this->rounding) {
            case 'LINE':
                $result['tax_amount'] = round($result['tax_amount'], 2);
                $result['total_amount'] = round($result['total_amount'], 2);
                break;
            case 'SUBTOTAL':
            case 'INVOICE':
                // These would be handled at a higher level
                break;
        }

        return $result;
    }
}
