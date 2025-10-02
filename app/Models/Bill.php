<?php

/**
 * Bill model for managing expense bills and reimbursements.
 *
 * @package App\Models
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Bill model for managing expense bills and reimbursements.
 *
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property string $purpose
 * @property string $status
 * @property string|null $description
 * @property string|null $vendor
 * @property string|null $receipt_number
 * @property \Carbon\Carbon $expense_date
 * @property string|null $category
 * @property string|null $payment_method
 * @property string|null $priority
 * @property string|null $notes
 * @property int|null $approved_by
 * @property \Carbon\Carbon|null $approved_at
 * @property string|null $rejected_reason
 */
class Bill extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'purpose',
        'status',
        'description',
        'vendor',
        'receipt_number',
        'expense_date',
        'category',
        'payment_method',
        'priority',
        'notes',
        'approved_by',
        'approved_at',
        'rejected_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
        'attachments' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * Get the user that owns the bill.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the bill.
     *
     * @return BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the bill files for the bill.
     *
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(BillFile::class);
    }

    /**
     * Get the approval logs for the bill.
     *
     * @return HasMany
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'entity_id')
            ->where('entity_type', 'bill');
    }

    /**
     * Scope a query to only include bills with a specific status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include bills for a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the formatted amount attribute.
     *
     * @return string
     */
    public function getFormattedAmountAttribute()
    {
        $symbol = $this->currency === 'BDT' ? '৳' : '$';
        return $symbol . ' ' . number_format($this->amount, 2);
    }

    /**
     * Get the status badge color.
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'Pending' => 'warning',
            'Approved' => 'success',
            'Forwarded' => 'info',
            'Paid' => 'primary',
            'Rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Check if the bill can be edited.
     *
     * @return bool
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['Pending', 'Rejected']);
    }

    /**
     * Check if the bill can be approved.
     *
     * @return bool
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'Pending';
    }

    /**
     * Check if the bill can be rejected.
     *
     * @return bool
     */
    public function canBeRejected(): bool
    {
        return in_array($this->status, ['Pending', 'Approved', 'Forwarded']);
    }

    /**
     * Check if the bill can be marked as paid.
     *
     * @return bool
     */
    public function canBeMarkedAsPaid(): bool
    {
        return in_array($this->status, ['Approved', 'Forwarded']);
    }

    /**
     * Check if the bill can be deleted.
     *
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        return in_array($this->status, ['Pending', 'Rejected']);
    }
}
