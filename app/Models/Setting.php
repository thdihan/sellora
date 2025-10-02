<?php

/**
 * Settings model for application configuration
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Setting model for storing application settings
 */
class Setting extends Model
{
    protected $fillable = [
        'type',
        'key_name',
        'value',
        'is_locked',
        'locked_by_role',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'value' => 'json',
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key The setting key
     * @param mixed $default Default value if setting not found
     * @return mixed The setting value or default
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key_name', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     *
     * @param string $key The setting key
     * @param mixed $value The setting value
     * @param string $type The setting type
     * @param bool $isLocked Whether the setting is locked
     * @param string|null $lockedByRole Role that can modify locked setting
     * @return Setting The created or updated setting
     */
    public static function set($key, $value, $type = 'string', $isLocked = false, $lockedByRole = null)
    {
        return static::updateOrCreate(
            ['key_name' => $key],
            [
                'value' => $value,
                'type' => $type,
                'is_locked' => $isLocked,
                'locked_by_role' => $lockedByRole,
            ]
        );
    }

    /**
     * Check if a setting is locked and user can modify it
     *
     * @param \App\Models\User $user The user to check permissions for
     * @return bool Whether the user can modify this setting
     */
    public function canModify($user)
    {
        if (!$this->is_locked) {
            return true;
        }

        return $user->hasRole($this->locked_by_role);
    }
}