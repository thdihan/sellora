<?php

/**
 * Location Tracking Policy
 *
 * Handles authorization for location tracking operations.
 * Implements role-based access control for pharma sales force.
 *
 * @category Policy
 * @package  Sellora\Policies
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Policies;

use App\Models\User;
use App\Models\LocationTracking;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Location Tracking Policy
 *
 * @category Policy
 * @package  Sellora\Policies
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class LocationTrackingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view any location data
     *
     * @param User $user The authenticated user
     *
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Author has unrestricted access, Managers and above can view team locations
        return $user->role && in_array($user->role->name, ['Author', 'ASM', 'RSM', 'ZSM', 'NSM', 'Admin']);
    }

    /**
     * Determine if user can view specific location data
     *
     * @param User             $user     The authenticated user
     * @param LocationTracking $location The location record
     *
     * @return bool
     */
    public function view(User $user, LocationTracking $location): bool
    {
        // Author role has unrestricted access
        if ($user->role && $user->role->name === 'Author') {
            return true;
        }
        
        // Users can view their own location data
        if ($user->id === $location->user_id) {
            return true;
        }
        
        // Managers can view subordinate locations
        return $this->_canViewSubordinateData($user, $location->user);
    }

    /**
     * Determine if user can create location data
     *
     * @param User $user The authenticated user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        // All authenticated users can create their own location data
        return true;
    }

    /**
     * Determine if user can update location data
     *
     * @param User             $user     The authenticated user
     * @param LocationTracking $location The location record
     *
     * @return bool
     */
    public function update(User $user, LocationTracking $location): bool
    {
        // Author role has unrestricted access
        if ($user->role && $user->role->name === 'Author') {
            return true;
        }
        
        // Users can only update their own location data
        // Managers cannot modify subordinate location data for integrity
        return $user->id === $location->user_id;
    }

    /**
     * Determine if user can delete location data
     *
     * @param User             $user     The authenticated user
     * @param LocationTracking $location The location record
     *
     * @return bool
     */
    public function delete(User $user, LocationTracking $location): bool
    {
        // Author and Admin can delete location data
        return $user->role && in_array($user->role->name, ['Author', 'Admin']);
    }

    /**
     * Determine if user can view team map
     *
     * @param User $user The authenticated user
     *
     * @return bool
     */
    public function viewTeamMap(User $user): bool
    {
        // Author has unrestricted access, Managers and above can view team map
        return $user->role && in_array($user->role->name, ['Author', 'ASM', 'RSM', 'ZSM', 'NSM', 'Admin']);
    }

    /**
     * Determine if user can view latest locations for team
     *
     * @param User $user The authenticated user
     *
     * @return bool
     */
    public function viewLatestLocations(User $user): bool
    {
        // Author has unrestricted access, Managers and above can view latest team locations
        return $user->role && in_array($user->role->name, ['Author', 'ASM', 'RSM', 'ZSM', 'NSM', 'Admin']);
    }

    /**
     * Determine if user can view historical location data
     *
     * @param User $user The authenticated user
     *
     * @return bool
     */
    public function viewHistory(User $user): bool
    {
        // Author has unrestricted access, ASM and above can view historical data
        return $user->role && in_array($user->role->name, ['Author', 'ASM', 'RSM', 'ZSM', 'NSM', 'Admin']);
    }

    /**
     * Check if user can view subordinate data
     *
     * @param User $manager     The manager user
     * @param User $subordinate The subordinate user
     *
     * @return bool
     */
    private function _canViewSubordinateData(User $manager, User $subordinate): bool
    {
        // Admin and Author can view all
        if ($manager->role && in_array($manager->role->name, ['Admin', 'Author'])) {
            return true;
        }
        
        // NSM can view RSM, ZSM, ASM, MR
        if ($manager->role === 'NSM') {
            return in_array($subordinate->role, ['RSM', 'ZSM', 'ASM', 'MR']);
        }
        
        // ZSM can view RSM, ASM, MR in their zone
        if ($manager->role === 'ZSM') {
            return in_array($subordinate->role, ['RSM', 'ASM', 'MR']) && 
                   $this->_isSameZone($manager, $subordinate);
        }
        
        // RSM can view ASM, MR in their region
        if ($manager->role === 'RSM') {
            return in_array($subordinate->role, ['ASM', 'MR']) && 
                   $this->_isSameRegion($manager, $subordinate);
        }
        
        // ASM can view MR in their area
        if ($manager->role === 'ASM') {
            return $subordinate->role === 'MR' && 
                   $this->_isSameArea($manager, $subordinate);
        }
        
        return false;
    }

    /**
     * Check if users are in the same zone
     *
     * @param User $user1 First user
     * @param User $user2 Second user
     *
     * @return bool
     */
    private function _isSameZone(User $user1, User $user2): bool
    {
        return isset($user1->zone_id) && isset($user2->zone_id) && 
               $user1->zone_id === $user2->zone_id;
    }

    /**
     * Check if users are in the same region
     *
     * @param User $user1 First user
     * @param User $user2 Second user
     *
     * @return bool
     */
    private function _isSameRegion(User $user1, User $user2): bool
    {
        return isset($user1->region_id) && isset($user2->region_id) && 
               $user1->region_id === $user2->region_id;
    }

    /**
     * Check if users are in the same area
     *
     * @param User $user1 First user
     * @param User $user2 Second user
     *
     * @return bool
     */
    private function _isSameArea(User $user1, User $user2): bool
    {
        return isset($user1->area_id) && isset($user2->area_id) && 
               $user1->area_id === $user2->area_id;
    }
}