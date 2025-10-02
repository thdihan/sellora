<?php

/**
 * PresentationView Model
 *
 * Tracks views for presentations to provide analytics
 *
 * @author Sellora Team
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresentationView extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'presentation_id',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the presentation that was viewed.
     *
     * @return BelongsTo
     */
    public function presentation(): BelongsTo
    {
        return $this->belongsTo(Presentation::class);
    }

    /**
     * Get the user who viewed the presentation.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
