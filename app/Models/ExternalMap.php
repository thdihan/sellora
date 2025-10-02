<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalMap extends Model
{
    protected $fillable = [
        'module',
        'external_id',
        'local_id',
        'source',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public static function findLocalId(string $module, string $externalId, string $source): ?int
    {
        $map = self::where('module', $module)
            ->where('external_id', $externalId)
            ->where('source', $source)
            ->first();

        return $map?->local_id;
    }

    public static function createMapping(string $module, string $externalId, int $localId, string $source, array $metadata = []): self
    {
        return self::create([
            'module' => $module,
            'external_id' => $externalId,
            'local_id' => $localId,
            'source' => $source,
            'metadata' => $metadata,
        ]);
    }
}
