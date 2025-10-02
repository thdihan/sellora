<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportItem extends Model
{
    protected $fillable = [
        'job_id',
        'module',
        'source_row_no',
        'payload',
        'status',
        'error_message',
        'entity_id',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(ImportJob::class, 'job_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isSkipped(): bool
    {
        return $this->status === 'skipped';
    }
}
