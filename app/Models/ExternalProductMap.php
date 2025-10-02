<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ExternalProductMap extends Model
{
    use HasFactory;

    protected $table = 'external_product_map';

    protected $fillable = [
        'product_id',
        'external_system',
        'external_id',
        'external_sku',
        'external_url',
        'external_data',
        'field_mapping',
        'sync_direction',
        'auto_sync',
        'last_synced_at',
        'last_sync_attempt_at',
        'sync_status',
        'sync_error',
        'sync_attempts',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'external_data' => 'array',
        'field_mapping' => 'array',
        'auto_sync' => 'boolean',
        'last_synced_at' => 'datetime',
        'last_sync_attempt_at' => 'datetime',
        'sync_attempts' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSystem($query, $system)
    {
        return $query->where('external_system', $system);
    }

    public function scopeAutoSync($query)
    {
        return $query->where('auto_sync', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('sync_status', $status);
    }

    public function scopePendingSync($query)
    {
        return $query->whereIn('sync_status', ['pending', 'failed'])
                    ->where('auto_sync', true)
                    ->where('is_active', true);
    }

    public function scopeNeedsRetry($query)
    {
        return $query->where('sync_status', 'failed')
                    ->where('sync_attempts', '<', 3)
                    ->where('auto_sync', true)
                    ->where('is_active', true);
    }

    public function markSyncStarted(): void
    {
        $this->update([
            'sync_status' => 'syncing',
            'last_sync_attempt_at' => now(),
            'sync_attempts' => $this->sync_attempts + 1,
        ]);
    }

    public function markSyncSuccess($data = null): void
    {
        $updateData = [
            'sync_status' => 'success',
            'last_synced_at' => now(),
            'sync_error' => null,
        ];
        
        if ($data) {
            $updateData['external_data'] = array_merge($this->external_data ?? [], $data);
        }
        
        $this->update($updateData);
    }

    public function markSyncFailed($error): void
    {
        $this->update([
            'sync_status' => 'failed',
            'sync_error' => $error,
        ]);
    }

    public function resetSyncAttempts(): void
    {
        $this->update([
            'sync_attempts' => 0,
            'sync_error' => null,
        ]);
    }

    public function canSync(): bool
    {
        return $this->is_active && 
               $this->auto_sync && 
               !in_array($this->sync_status, ['syncing', 'disabled']);
    }

    public function shouldRetry(): bool
    {
        return $this->sync_status === 'failed' && 
               $this->sync_attempts < 3 && 
               $this->auto_sync && 
               $this->is_active;
    }

    public function getExternalUrlAttribute($value): ?string
    {
        if ($value) {
            return $value;
        }
        
        // Generate URL based on external system and ID
        return $this->generateExternalUrl();
    }

    public function generateExternalUrl(): ?string
    {
        if (!$this->external_id) {
            return null;
        }
        
        $baseUrls = [
            'shopify' => 'https://{shop}.myshopify.com/admin/products/{id}',
            'woocommerce' => '{site_url}/wp-admin/post.php?post={id}&action=edit',
            'magento' => '{admin_url}/catalog/product/edit/id/{id}',
            'amazon' => 'https://sellercentral.amazon.com/inventory/ref=xx_invmgr_dnav_xx?tbla_myitable=sort:%7B%22sortOrder%22%3A%22DESCENDING%22%2C%22sortedColumnId%22%3A%22date%22%7D;search:{id}',
        ];
        
        if (!isset($baseUrls[$this->external_system])) {
            return null;
        }
        
        $url = $baseUrls[$this->external_system];
        $url = str_replace('{id}', $this->external_id, $url);
        
        // Replace placeholders with actual values from external_data
        if ($this->external_data) {
            foreach ($this->external_data as $key => $value) {
                $url = str_replace('{' . $key . '}', $value, $url);
            }
        }
        
        return $url;
    }

    public function getLastSyncStatusAttribute(): string
    {
        if (!$this->last_synced_at) {
            return 'Never synced';
        }
        
        $diff = $this->last_synced_at->diffForHumans();
        return "Last synced {$diff}";
    }
}
