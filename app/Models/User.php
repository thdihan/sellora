<?php

/**
 * User model file
 *
 * PHP version 8.1
 *
 * @category Models
 * @package  App\Models
 * @author   Sellora Team <team@sellora.com> <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

/**
 * User model class
 *
 * @category Models
 * @package  App\Models
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'employee_id',
        'password',
        'role_id',
        'designation',
        'photo',
        'date_of_birth',
        'date_of_joining',
        'blood_group',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'address',
        'phone',
        'bio',
        'timezone',
        'email_notifications',
        'sms_notifications',
        'marketing_emails',
        'security_alerts',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'date_of_joining' => 'date',
            'last_login_at' => 'datetime',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'security_alerts' => 'boolean',
        ];
    }

    /**
     * Get the role that owns the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the locations for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Get the location visits for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locationVisits()
    {
        return $this->hasMany(LocationVisit::class);
    }

    /**
     * Check if user has a specific role.
     *
     * @param string $roleName The role name to check
     *
     * @return bool
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user has any of the given roles.
     *
     * @param array $roles Array of role names to check
     *
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        if (!$this->role) {
            return false;
        }
        
        return in_array($this->role->name, (array) $roles);
    }

    /**
     * Check if this user is the system owner.
     *
     * @return bool
     */
    public function isOwner(): bool
    {
        $ownerEmail = env('BOOTSTRAP_OWNER_EMAIL', 'rayhand2k@gmail.com');
        return $this->email === $ownerEmail && $this->hasRole('Author');
    }

    /**
     * Check if owner mutation is allowed.
     *
     * @return bool
     */
    public function canMutateOwner(): bool
    {
        return env('ALLOW_OWNER_MUTATION', false) === true;
    }

    /**
     * Override delete to protect owner account.
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->isOwner() && !$this->canMutateOwner()) {
            Log::warning(
                'Attempted to delete owner account',
                [
                    'user_id' => $this->id,
                    'email' => $this->email,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'timestamp' => now()
                ]
            );
            
            throw new \Exception('Owner account cannot be deleted. Set ALLOW_OWNER_MUTATION=true to override.');
        }
        
        return parent::delete();
    }

    /**
     * Override update to protect owner role changes.
     *
     * @param array $attributes The attributes to update
     * @param array $options    Update options
     *
     * @return bool
     * @throws \Exception
     */
    public function update(array $attributes = [], array $options = [])
    {
        if ($this->isOwner() && !$this->canMutateOwner()) {
            // Check if role is being changed
            if (isset($attributes['role_id']) && $attributes['role_id'] != $this->role_id) {
                Log::warning(
                    'Attempted to change owner role',
                    [
                        'user_id' => $this->id,
                        'email' => $this->email,
                        'old_role_id' => $this->role_id,
                        'new_role_id' => $attributes['role_id'],
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'timestamp' => now()
                    ]
                );
                
                throw new \Exception('Owner role cannot be changed. Set ALLOW_OWNER_MUTATION=true to override.');
            }
        }
        
        return parent::update($attributes, $options);
    }
}
