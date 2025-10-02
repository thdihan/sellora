<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LocationVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'user_id',
        'visited_at',
        'left_at',
        'duration_minutes',
        'purpose',
        'notes',
        'check_in_method',
        'check_out_method',
        'latitude',
        'longitude',
        'accuracy',
        'weather',
        'temperature',
        'mood_rating',
        'productivity_rating',
        'photos',
        'tags'
    ];

    protected $casts = [
        'visited_at' => 'datetime',
        'left_at' => 'datetime',
        'duration_minutes' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'temperature' => 'decimal:1',
        'mood_rating' => 'integer',
        'productivity_rating' => 'integer',
        'photos' => 'array',
        'tags' => 'array'
    ];

    protected $appends = [
        'formatted_duration',
        'is_current_visit',
        'visit_summary'
    ];

    // Relationships
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_minutes) {
            return 'Unknown';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    public function getIsCurrentVisitAttribute(): bool
    {
        return $this->visited_at && !$this->left_at;
    }

    public function getVisitSummaryAttribute(): string
    {
        $summary = "Visited {$this->location->name}";
        
        if ($this->purpose) {
            $summary .= " for {$this->purpose}";
        }
        
        if ($this->duration_minutes) {
            $summary .= " ({$this->formatted_duration})";
        }
        
        return $summary;
    }

    // Scopes
    public function scopeCurrent($query)
    {
        return $query->whereNotNull('visited_at')->whereNull('left_at');
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('visited_at')->whereNotNull('left_at');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('visited_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('visited_at', now()->month)
                    ->whereYear('visited_at', now()->year);
    }

    public function scopeByPurpose($query, $purpose)
    {
        return $query->where('purpose', $purpose);
    }

    public function scopeLongVisits($query, $minimumMinutes = 60)
    {
        return $query->where('duration_minutes', '>=', $minimumMinutes);
    }

    public function scopeShortVisits($query, $maximumMinutes = 15)
    {
        return $query->where('duration_minutes', '<=', $maximumMinutes);
    }

    /**
     * Scope to get recent visits within specified days.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days Number of days to look back
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('visited_at', '>=', now()->subDays($days));
    }

    // Methods
    public function checkOut(): bool
    {
        if ($this->left_at) {
            return false; // Already checked out
        }

        $leftAt = now();
        $duration = $this->visited_at->diffInMinutes($leftAt);

        $this->update([
            'left_at' => $leftAt,
            'duration_minutes' => $duration,
            'check_out_method' => 'manual'
        ]);

        return true;
    }

    public function calculateDuration(): int
    {
        if (!$this->visited_at) {
            return 0;
        }

        $endTime = $this->left_at ?? now();
        return $this->visited_at->diffInMinutes($endTime);
    }

    public function addPhoto(string $photoPath): void
    {
        $photos = $this->photos ?? [];
        $photos[] = $photoPath;
        $this->update(['photos' => $photos]);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        $this->update(['tags' => array_values($tags)]);
    }

    public function setMoodRating(int $rating): void
    {
        if ($rating >= 1 && $rating <= 5) {
            $this->update(['mood_rating' => $rating]);
        }
    }

    public function setProductivityRating(int $rating): void
    {
        if ($rating >= 1 && $rating <= 5) {
            $this->update(['productivity_rating' => $rating]);
        }
    }

    public function getTimeSpentFormatted(): string
    {
        if ($this->is_current_visit) {
            $currentDuration = $this->calculateDuration();
            $hours = floor($currentDuration / 60);
            $minutes = $currentDuration % 60;
            
            if ($hours > 0) {
                return "Currently here for {$hours}h {$minutes}m";
            }
            return "Currently here for {$minutes}m";
        }

        return $this->formatted_duration;
    }

    public static function createVisit(Location $location, array $data = []): self
    {
        return self::create(array_merge([
            'location_id' => $location->id,
            'user_id' => auth()->id(),
            'visited_at' => now(),
            'check_in_method' => 'manual'
        ], $data));
    }
}
