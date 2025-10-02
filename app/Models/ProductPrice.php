<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ProductPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'price_type',
        'price',
        'cost_price',
        'min_quantity',
        'max_quantity',
        'currency',
        'customer_id',
        'customer_group_id',
        'valid_from',
        'valid_to',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query, $date = null)
    {
        $date = $date ?: now();
        return $query->where(function ($q) use ($date) {
            $q->where('valid_from', '<=', $date)
              ->where(function ($subQ) use ($date) {
                  $subQ->whereNull('valid_to')
                       ->orWhere('valid_to', '>=', $date);
              });
        });
    }

    public function scopeForQuantity($query, $quantity)
    {
        return $query->where(function ($q) use ($quantity) {
            $q->where('min_quantity', '<=', $quantity)
              ->where(function ($subQ) use ($quantity) {
                  $subQ->whereNull('max_quantity')
                       ->orWhere('max_quantity', '>=', $quantity);
              });
        });
    }

    public function scopeForCustomer($query, $customerId = null, $customerGroupId = null)
    {
        return $query->where(function ($q) use ($customerId, $customerGroupId) {
            $q->whereNull('customer_id')
              ->whereNull('customer_group_id');
            
            if ($customerId) {
                $q->orWhere('customer_id', $customerId);
            }
            
            if ($customerGroupId) {
                $q->orWhere('customer_group_id', $customerGroupId);
            }
        });
    }

    public function isValid($date = null): bool
    {
        $date = $date ?: now();
        
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->valid_from && $this->valid_from->gt($date)) {
            return false;
        }
        
        if ($this->valid_to && $this->valid_to->lt($date)) {
            return false;
        }
        
        return true;
    }

    public function isValidForQuantity($quantity): bool
    {
        if ($this->min_quantity && $quantity < $this->min_quantity) {
            return false;
        }
        
        if ($this->max_quantity && $quantity > $this->max_quantity) {
            return false;
        }
        
        return true;
    }

    public function getMarginAttribute(): float
    {
        if (!$this->cost_price || $this->cost_price <= 0) {
            return 0;
        }
        
        return (($this->price - $this->cost_price) / $this->cost_price) * 100;
    }

    public function getProfitAttribute(): float
    {
        return $this->price - ($this->cost_price ?: 0);
    }
}
