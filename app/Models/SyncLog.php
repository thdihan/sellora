<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class SyncLog extends Model
{
    use HasFactory;

    protected $table = 'sync_log';

    protected $fillable = [
        'sync_type',
        'external_system',
        'operation',
        'syncable_type',
        'syncable_id',
        'external_id',
        'status',
        'started_at',
        'completed_at',
        'duration_ms',
        'request_data',
        'response_data',
        'error_message',
        'error_details',
        'retry_count',
        'batch_id',
        'user_id',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_ms' => 'integer',
        'request_data' => 'array',
        'response_data' => 'array',
        'error_details' => 'array',
        'retry_count' => 'integer',
    ];

    public function syncable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('sync_type', $type);
    }

    public function scopeBySystem($query, $system)
    {
        return $query->where('external_system', $system);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByOperation($query, $operation)
    {
        return $query->where('operation', $operation);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeByBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeSlowSyncs($query, $thresholdMs = 5000)
    {
        return $query->where('duration_ms', '>', $thresholdMs);
    }

    public function markStarted(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    public function markCompleted($responseData = null): void
    {
        $completedAt = now();
        $duration = $this->started_at ? 
            $this->started_at->diffInMilliseconds($completedAt) : null;

        $updateData = [
            'status' => 'success',
            'completed_at' => $completedAt,
            'duration_ms' => $duration,
        ];

        if ($responseData) {
            $updateData['response_data'] = $responseData;
        }

        $this->update($updateData);
    }

    public function markFailed($errorMessage, $errorDetails = null): void
    {
        $completedAt = now();
        $duration = $this->started_at ? 
            $this->started_at->diffInMilliseconds($completedAt) : null;

        $updateData = [
            'status' => 'failed',
            'completed_at' => $completedAt,
            'duration_ms' => $duration,
            'error_message' => $errorMessage,
        ];

        if ($errorDetails) {
            $updateData['error_details'] = $errorDetails;
        }

        $this->update($updateData);
    }

    public function incrementRetry(): void
    {
        $this->increment('retry_count');
        $this->update(['status' => 'pending']);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['success', 'failed']);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->duration_ms) {
            return null;
        }

        if ($this->duration_ms < 1000) {
            return $this->duration_ms . 'ms';
        }

        return round($this->duration_ms / 1000, 2) . 's';
    }

    public function getFormattedStatusAttribute(): string
    {
        $statusMap = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'success' => 'Success',
            'failed' => 'Failed',
        ];

        return $statusMap[$this->status] ?? ucfirst($this->status);
    }

    public static function createForSync(
        string $syncType,
        string $externalSystem,
        string $operation,
        Model $syncable,
        ?string $externalId = null,
        ?array $requestData = null,
        ?string $batchId = null,
        ?int $userId = null
    ): self {
        return self::create([
            'sync_type' => $syncType,
            'external_system' => $externalSystem,
            'operation' => $operation,
            'syncable_type' => get_class($syncable),
            'syncable_id' => $syncable->id,
            'external_id' => $externalId,
            'status' => 'pending',
            'request_data' => $requestData,
            'batch_id' => $batchId,
            'user_id' => $userId,
            'ip_address' => request()?->ip(),
        ]);
    }

    public static function getBatchStats(string $batchId): array
    {
        $logs = self::where('batch_id', $batchId)->get();
        
        return [
            'total' => $logs->count(),
            'success' => $logs->where('status', 'success')->count(),
            'failed' => $logs->where('status', 'failed')->count(),
            'pending' => $logs->where('status', 'pending')->count(),
            'in_progress' => $logs->where('status', 'in_progress')->count(),
            'average_duration' => $logs->where('duration_ms', '>', 0)->avg('duration_ms'),
        ];
    }
}
