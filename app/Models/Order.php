<?php

/**
 * Order Model
 *
 * Represents an order in the system with status workflow,
 * file attachments, and approval tracking.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Order model for managing sales orders
 *
 * @property int $id
 * @property int $user_id
 * @property string $order_number
 * @property string $customer_name
 * @property decimal $amount
 * @property string $status
 * @property string|null $description
 * @property string|null $notes
 * @property int|null $approved_by
 * @property \Carbon\Carbon|null $approved_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Order extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_FORWARDED = 'Forwarded';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_CANCELLED = 'Cancelled';

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'amount',
        'status',
        'description',
        'notes',
        'approved_by',
        'approved_at',
        'tax_breakdown',
        'total_amount',
        'vat_condition',
        'tax_condition',
        'vat_amount',
        'tax_amount',
        'net_revenue'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'tax_breakdown' => 'array',
        'total_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_revenue' => 'decimal:2'
    ];

    /**
     * Get the user that owns the order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the files for the order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(OrderFile::class);
    }

    /**
     * Get the tax lines for the order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderTaxLines()
    {
        return $this->hasMany(OrderTaxLine::class);
    }

    /**
     * Get the order items for the order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the status badge attribute
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_APPROVED => 'badge-info',
            self::STATUS_FORWARDED => 'badge-primary',
            self::STATUS_COMPLETED => 'badge-success',
            self::STATUS_CANCELLED => 'badge-danger'
        ];

        return $badges[$this->status] ?? 'badge-secondary';
    }

    /**
     * Get the payment status badge attribute
     *
     * @return string
     */
    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'Pending' => 'badge-warning',
            'Paid' => 'badge-success',
            'Failed' => 'badge-danger'
        ];

        return $badges[$this->payment_status ?? 'Pending'] ?? 'badge-secondary';
    }

    /**
     * Boot the model
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(
            function ($order) {
                if (!$order->order_number) {
                    $order->order_number = 'ORD-' . strtoupper(Str::random(8));
                }
            }
        );
    }
}
