<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'from_role',
        'to_role',
        'action',
        'remarks',
        'acted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'entity_id' => 'integer',
    ];

    /**
     * Get the user who acted on the approval.
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acted_by');
    }

    /**
     * Get the related entity (polymorphic relationship).
     */
    public function entity()
    {
        return match($this->entity_type) {
            'order' => $this->belongsTo(Order::class, 'entity_id'),
            'bill' => $this->belongsTo(Bill::class, 'entity_id'),
            'budget' => $this->belongsTo(Budget::class, 'entity_id'),
            default => null
        };
    }

    /**
     * Scope a query to only include approvals for a specific entity type.
     */
    public function scopeForEntityType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope a query to only include approvals for a specific entity.
     */
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)
                    ->where('entity_id', $entityId);
    }

    /**
     * Get the action badge color.
     */
    public function getActionBadgeAttribute()
    {
        return match($this->action) {
            'approve' => 'success',
            'reject' => 'danger',
            'forward' => 'info',
            default => 'secondary'
        };
    }
}