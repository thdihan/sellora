<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SelfAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period',
        'targets',
        'achievements',
        'problems',
        'solutions',
        'market_analysis',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'reviewer_comments'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = [
        'status_badge',
        'is_editable',
        'days_since_submission'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'submitted' => '<span class="badge bg-warning">Submitted</span>',
            'reviewed' => '<span class="badge bg-success">Reviewed</span>',
            default => '<span class="badge bg-light">Unknown</span>'
        };
    }

    public function getIsEditableAttribute(): bool
    {
        return $this->status === 'draft';
    }

    public function getDaysSinceSubmissionAttribute(): ?int
    {
        if (!$this->submitted_at) {
            return null;
        }
        return $this->submitted_at->diffInDays(now());
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', ['submitted', 'reviewed']);
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeRecentlySubmitted($query, $days = 30)
    {
        return $query->where('submitted_at', '>=', now()->subDays($days));
    }

    // Methods
    public function canBeEditedBy($userId): bool
    {
        return $this->user_id === $userId && $this->is_editable;
    }

    public function canBeViewedBy($userId): bool
    {
        // Users can view their own assessments
        if ($this->user_id === $userId) {
            return true;
        }

        // Managers can view their team's assessments
        $user = User::find($userId);
        if ($user && $user->role) {
            $managerRoles = ['Admin', 'Manager', 'ASM', 'RSM', 'ZSM', 'NSM', 'AGM', 'DGM', 'GM', 'ED', 'Director', 'Chairman'];
            return in_array($user->role->name, $managerRoles);
        }

        return false;
    }

    public function submit(): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }

        return $this->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);
    }

    public function markAsReviewed($reviewerId, $comments = null): bool
    {
        if ($this->status !== 'submitted') {
            return false;
        }

        return $this->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
            'reviewer_comments' => $comments
        ]);
    }

    public function revertToDraft(): bool
    {
        if ($this->status === 'draft') {
            return false;
        }

        return $this->update([
            'status' => 'draft',
            'submitted_at' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
            'reviewer_comments' => null
        ]);
    }

    // Static methods
    public static function getAvailablePeriods(): array
    {
        $currentYear = now()->year;
        $periods = [];
        
        // Add quarterly periods
        for ($quarter = 1; $quarter <= 4; $quarter++) {
            $periods[] = "Q{$quarter} {$currentYear}";
        }
        
        // Add monthly periods for current year
        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($currentYear, $month, 1)->format('F');
            $periods[] = "{$monthName} {$currentYear}";
        }
        
        // Add yearly period
        $periods[] = (string) $currentYear;
        
        return $periods;
    }
}