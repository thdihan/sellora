<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'latitude',
        'longitude',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'type',
        'status',
        'accuracy',
        'altitude',
        'speed',
        'heading',
        'timestamp',
        'ip_address',
        'user_agent',
        'notes',
        'is_favorite',
        'visit_count',
        'last_visited_at'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'altitude' => 'decimal:2',
        'speed' => 'decimal:2',
        'heading' => 'decimal:2',
        'timestamp' => 'datetime',
        'is_favorite' => 'boolean',
        'visit_count' => 'integer',
        'last_visited_at' => 'datetime'
    ];

    protected $appends = [
        'formatted_address',
        'distance_from_user',
        'google_maps_url'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(LocationVisit::class);
    }

    // Accessors
    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);
        
        return implode(', ', $parts);
    }

    public function getDistanceFromUserAttribute(): ?float
    {
        if (!auth()->user() || !auth()->user()->current_latitude || !auth()->user()->current_longitude) {
            return null;
        }

        return $this->calculateDistance(
            auth()->user()->current_latitude,
            auth()->user()->current_longitude,
            $this->latitude,
            $this->longitude
        );
    }

    public function getGoogleMapsUrlAttribute(): string
    {
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }

    // Scopes
    public function scopeNearby($query, $latitude, $longitude, $radius = 10)
    {
        return $query->selectRaw(
            "*, (
                6371 * acos(
                    cos(radians(?))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?))
                    * sin(radians(latitude))
                )
            ) AS distance",
            [$latitude, $longitude, $latitude]
        )
        ->having('distance', '<', $radius)
        ->orderBy('distance');
    }

    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Methods
    public function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    public function reverseGeocode(): array
    {
        try {
            $response = Http::get('https://api.opencagedata.com/geocode/v1/json', [
                'q' => "{$this->latitude},{$this->longitude}",
                'key' => config('services.opencage.key'),
                'limit' => 1
            ]);

            if ($response->successful() && $response->json('results')) {
                $result = $response->json('results')[0];
                $components = $result['components'] ?? [];

                return [
                    'address' => $result['formatted'] ?? '',
                    'city' => $components['city'] ?? $components['town'] ?? $components['village'] ?? '',
                    'state' => $components['state'] ?? $components['province'] ?? '',
                    'country' => $components['country'] ?? '',
                    'postal_code' => $components['postcode'] ?? ''
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Reverse geocoding failed: ' . $e->getMessage());
        }

        return [];
    }

    public function updateAddressFromCoordinates(): bool
    {
        $addressData = $this->reverseGeocode();
        
        if (!empty($addressData)) {
            $this->update($addressData);
            return true;
        }
        
        return false;
    }

    public function markAsFavorite(): void
    {
        $this->update(['is_favorite' => true]);
    }

    public function unmarkAsFavorite(): void
    {
        $this->update(['is_favorite' => false]);
    }

    public function incrementVisitCount(): void
    {
        $this->increment('visit_count');
        $this->update(['last_visited_at' => now()]);
    }

    public function getFormattedDistance($unit = 'km'): string
    {
        $distance = $this->distance_from_user;
        
        if ($distance === null) {
            return 'Unknown';
        }

        if ($unit === 'miles') {
            $distance = $distance * 0.621371;
            return number_format($distance, 1) . ' miles';
        }

        if ($distance < 1) {
            return number_format($distance * 1000, 0) . ' m';
        }

        return number_format($distance, 1) . ' km';
    }

    public function toGeoJson(): array
    {
        return [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [$this->longitude, $this->latitude]
            ],
            'properties' => [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'type' => $this->type,
                'address' => $this->formatted_address,
                'is_favorite' => $this->is_favorite,
                'visit_count' => $this->visit_count
            ]
        ];
    }
}
