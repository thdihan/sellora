<?php

/**
 * Presentation Model
 * 
 * Handles presentation file management, sharing, and analytics
 * 
 * @package App\Models
 * @author Sellora Team
 * @version 1.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Class Presentation
 * 
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $file_path
 * @property string $file_name
 * @property int $file_size
 * @property string $file_type
 * @property string $category
 * @property array $tags
 * @property string $status
 * @property string $privacy_level
 * @property bool $is_template
 * @property int $user_id
 * @property int $view_count
 * @property int $download_count
 * @property string $version
 * @property int $original_presentation_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $deleted_at
 */
class Presentation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'content',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'thumbnail_path',
        'status',
        'category',
        'tags',
        'presentation_date',
        'duration_minutes',
        'audience_size',
        'location',
        'notes',
        'is_template',
        'template_category',
        'view_count',
        'download_count',
        'last_viewed_at',
        'shared_with',
        'privacy_level',
        'version',
        'original_presentation_id'
    ];

    protected $casts = [
        'presentation_date' => 'datetime',
        'last_viewed_at' => 'datetime',
        'is_template' => 'boolean',
        'view_count' => 'integer',
        'download_count' => 'integer',
        'duration_minutes' => 'integer',
        'audience_size' => 'integer',
        'file_size' => 'integer',
        'tags' => 'array',
        'shared_with' => 'array'
    ];

    protected $appends = [
        'formatted_file_size',
        'file_url',
        'thumbnail_url',
        'is_recent',
        'status_badge'
    ];

    // Relationships
    /**
     * Get the user who owns the presentation.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all views for this presentation.
     *
     * @return HasMany
     */
    public function views(): HasMany
    {
        return $this->hasMany(PresentationView::class);
    }

    /**
     * Get all downloads for this presentation.
     *
     * @return HasMany
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(PresentationDownload::class);
    }

    /**
     * Get all comments for this presentation.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(PresentationComment::class);
    }

    /**
     * Get the original presentation if this is a version.
     *
     * @return BelongsTo
     */
    public function originalPresentation(): BelongsTo
    {
        return $this->belongsTo(Presentation::class, 'original_presentation_id');
    }

    /**
     * Get all versions of this presentation.
     *
     * @return HasMany
     */
    public function versions(): HasMany
    {
        return $this->hasMany(Presentation::class, 'original_presentation_id');
    }

    /**
     * Get all shares for this presentation.
     *
     * @return HasMany
     */
    public function shares(): HasMany
    {
        return $this->hasMany(PresentationShare::class);
    }

    /**
     * Get formatted file size
     * 
     * @return string
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Get file URL
     * 
     * @return string|null
     */
    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::url($this->file_path);
    }

    /**
     * Get thumbnail URL based on file type
     * 
     * @return string
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return asset('images/presentation-placeholder.png');
        }

        return Storage::url($this->thumbnail_path);
    }

    /**
     * Check if presentation is recent (within 7 days)
     * 
     * @return bool
     */
    public function getIsRecentAttribute(): bool
    {
        return $this->created_at && $this->created_at->isAfter(now()->subDays(7));
    }

    /**
     * Get status badge CSS class
     * 
     * @return string
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => 'badge-secondary',
            'active' => 'badge-success',
            'archived' => 'badge-warning',
            'deleted' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePublic($query)
    {
        return $query->where('privacy_level', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('privacy_level', 'private');
    }

    public function scopeShared($query)
    {
        return $query->where('privacy_level', 'shared');
    }

    /**
     * Increment view count and record view
     * 
     * @param int|null $userId
     * @return void
     */
    public function incrementViewCount($userId = null): void
    {
        $this->increment('view_count');
        $this->update(['last_viewed_at' => now()]);

        if ($userId) {
            $this->views()->create([
                'user_id' => $userId,
                'viewed_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
    }

    /**
     * Increment download count and record download
     * 
     * @param int|null $userId
     * @return void
     */
    public function incrementDownloadCount($userId = null): void
    {
        $this->increment('download_count');

        if ($userId) {
            $this->downloads()->create([
                'user_id' => $userId,
                'downloaded_at' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
    }

    /**
     * Check if presentation can be edited by user
     * 
     * @param int $userId
     * @return bool
     */
    public function canBeEditedBy($userId): bool
    {
        $user = auth()->user();
        
        // Owner can always edit
        if ($this->user_id === $userId) {
            return true;
        }
        
        // Admin can edit all
        if ($user?->role?->name === 'Admin' || $user?->role?->name === 'Author') {
            return true;
        }
        
        // NSM+ roles can manage all presentations
        $nsmPlusRoles = ['NSM', 'ZSM', 'RSM', 'ASM', 'AGM', 'DGM', 'GM', 'ED', 'Director', 'Chairman'];
        if ($user?->role && in_array($user->role->name, $nsmPlusRoles)) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if presentation can be viewed by user
     * 
     * @param int $userId
     * @return bool
     */
    public function canBeViewedBy($userId): bool
    {
        if ($this->privacy_level === 'public') {
            return true;
        }

        if ($this->privacy_level === 'private') {
            return $this->user_id === $userId || (auth()->user()?->role && auth()->user()->role->name === 'Admin');
        }

        if ($this->privacy_level === 'shared') {
            $sharedWith = $this->shared_with ?? [];
            return in_array($userId, $sharedWith) || $this->user_id === $userId || (auth()->user()?->role && auth()->user()->role->name === 'Admin');
        }

        return false;
    }

    /**
     * Create a new version of the presentation
     * 
     * @param array $newData
     * @return self
     */
    public function createVersion($newData): self
    {
        $version = $this->replicate();
        $version->fill($newData);
        $version->original_presentation_id = $this->id;
        $version->version = $this->getNextVersionNumber();
        $version->save();

        return $version;
    }

    /**
     * Get next version number
     * 
     * @return int
     */
    public function getNextVersionNumber(): int
    {
        $latestVersion = $this->versions()->max('version') ?? 0;
        return $latestVersion + 1;
    }

    /**
     * Archive the presentation
     * 
     * @return void
     */
    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    /**
     * Restore the presentation
     * 
     * @return void
     */
    public function restore(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Make presentation a template
     * 
     * @param string|null $category
     * @return void
     */
    public function makeTemplate($category = null): void
    {
        $this->update([
            'is_template' => true,
            'template_category' => $category,
            'privacy_level' => 'public'
        ]);
    }

    /**
     * Remove from templates
     * 
     * @return void
     */
    public function removeTemplate(): void
    {
        $this->update([
            'is_template' => false,
            'template_category' => null
        ]);
    }

    /**
     * Share presentation with users
     * 
     * @param array $userIds
     * @return void
     */
    public function shareWith(array $userIds): void
    {
        $this->update([
            'shared_with' => array_unique(array_merge($this->shared_with ?? [], $userIds)),
            'privacy_level' => 'shared'
        ]);
    }

    /**
     * Unshare presentation with users
     * 
     * @param array $userIds
     * @return void
     */
    public function unshareWith(array $userIds): void
    {
        $currentShared = $this->shared_with ?? [];
        $newShared = array_diff($currentShared, $userIds);
        
        $this->update([
            'shared_with' => $newShared,
            'privacy_level' => empty($newShared) ? 'private' : 'shared'
        ]);
    }

    /**
     * Delete associated files
     * 
     * @return void
     */
    public function deleteFile(): void
    {
        if ($this->file_path && Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }

        if ($this->thumbnail_path && Storage::exists($this->thumbnail_path)) {
            Storage::delete($this->thumbnail_path);
        }
    }

    /**
     * Boot method to handle model events
     * 
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($presentation) {
            $presentation->deleteFile();
        });
    }
}