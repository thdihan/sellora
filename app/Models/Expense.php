<?php

/**
 * Expense Model
 *
 * This file contains the Expense model for managing expense records
 * in the Sellora application.
 *
 * @category Model
 * @package  App\Models
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Expense Model
 *
 * Represents an expense record in the system with approval workflow,
 * status tracking, and financial calculations.
 *
 * @category Model
 * @package  App\Models
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'amount',
        'currency',
        'expense_date',
        'receipt_number',
        'vendor',
        'status',
        'priority',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason',
        'attachments',
        'notes',
        'is_reimbursable',
        'tax_amount',
        'payment_method',
        'reference_number',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'approved_at' => 'datetime',
        'attachments' => 'array',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'is_reimbursable' => 'boolean',
    ];

    /**
     * Get the user who created this expense
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved this expense
     *
     * @return BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the category for this expense
     * Note: This creates a virtual relationship since category is stored as a string
     *
     * @param string $value The category value
     *
     * @return object
     */
    public function getCategoryAttribute($value)
    {
        return (object) ['name' => $value];
    }

    /**
     * Calculate the total amount including tax
     *
     * @return Attribute
     */
    public function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->amount + ($this->tax_amount ?? 0)
        );
    }

    /**
     * Check if the expense is approved
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the expense is pending approval
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the expense is rejected
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the expense is paid
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if the expense can be approved
     *
     * @return bool
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get the color class for the expense status
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'paid' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get the color class for the expense priority
     *
     * @return string
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get the total amount including tax
     *
     * @return float
     */
    public function getTotalAmount(): float
    {
        return $this->amount + ($this->tax_amount ?? 0);
    }
}
