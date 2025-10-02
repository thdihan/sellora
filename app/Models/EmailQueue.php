<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmailQueue extends Model
{
    protected $table = 'email_queue';
    
    protected $fillable = [
        'to_email',
        'to_user_id',
        'subject',
        'body',
        'template_slug',
        'data_json',
        'scheduled_at',
        'sent_at',
        'status',
        'error',
        'attempts'
    ];
    
    protected $casts = [
        'data_json' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
    
    /**
     * Get the user that this email is for
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
    
    /**
     * Scope for queued emails that are due to be sent
     */
    public function scopeDue($query)
    {
        return $query->where('status', 'queued')
                    ->where('scheduled_at', '<=', now());
    }
    
    /**
     * Scope for failed emails
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
    
    /**
     * Mark email as sent
     */
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error' => null
        ]);
    }
    
    /**
     * Mark email as failed
     */
    public function markAsFailed(string $error)
    {
        $this->update([
            'status' => 'failed',
            'error' => $error,
            'attempts' => $this->attempts + 1
        ]);
    }
}
