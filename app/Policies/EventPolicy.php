<?php

/**
 * Event Policy for managing access control to Event resources.
 * 
 * This policy implements role-based access control where users can manage
 * their own events and higher-level roles can access subordinate events.
 * 
 * @package App\Policies
 * @author  Sellora Team
 * @since   1.0.0
 */

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Event Policy Class
 * 
 * Handles authorization for Event model operations including view, create,
 * update, and delete permissions based on user roles and ownership.
 */
class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user The authenticated user
     * @return bool True if user can view events
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view events
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User  $user  The authenticated user
     * @param Event $event The event model instance
     * @return bool True if user can view the event
     */
    public function view(User $user, Event $event): bool
    {
        // Author users can view any event
        if ($user->role?->name === 'Author') {
            return true;
        }
        
        // Users can view their own events or events of their subordinates
        if ($event->created_by === $user->id) {
            return true;
        }

        // Check if user can view subordinate events based on role hierarchy
        return $this->canViewSubordinateEvents($user, $event);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user The authenticated user
     * @return bool True if user can create events
     */
    public function create(User $user): bool
    {
        // All authenticated users can create events
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User  $user  The authenticated user
     * @param Event $event The event model instance
     * @return bool True if user can update the event
     */
    public function update(User $user, Event $event): bool
    {
        // Author users can update any event
        if ($user->role?->name === 'Author') {
            return true;
        }
        
        // Users can update their own events or events of their subordinates
        if ($event->created_by === $user->id) {
            return true;
        }

        return $this->canManageSubordinateEvents($user, $event);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User  $user  The authenticated user
     * @param Event $event The event model instance
     * @return bool True if user can delete the event
     */
    public function delete(User $user, Event $event): bool
    {
        // Author users can delete any event
        if ($user->role?->name === 'Author') {
            return true;
        }
        
        // Users can delete their own events or events of their subordinates
        if ($event->created_by === $user->id) {
            return true;
        }

        return $this->canManageSubordinateEvents($user, $event);
    }

    /**
     * Check if user can view events of subordinate users.
     * 
     * Implements role hierarchy where higher-level roles can view
     * events created by lower-level roles.
     *
     * @param User  $user  The authenticated user
     * @param Event $event The event to check access for
     * @return bool True if user can view subordinate events
     */
    private function canViewSubordinateEvents(User $user, Event $event): bool
    {
        $eventCreator = User::find($event->created_by);
        if (!$eventCreator) {
            return false;
        }

        // Role hierarchy: Author (highest) > Admin > NSM+ roles > other roles
        $roleHierarchy = [
            'Author' => 10,
            'Admin' => 9,
            'Chairman' => 8,
            'Director' => 7,
            'ED' => 6,
            'GM' => 5,
            'DGM' => 4,
            'AGM' => 3,
            'NSM' => 3,
            'ZSM' => 2,
            'RSM' => 2,
            'ASM' => 2,
            'MPO' => 1,
            'MR' => 1,
            'Trainee' => 1
        ];

        $userRoleName = $user->role?->name ?? '';
        $creatorRoleName = $eventCreator->role?->name ?? '';
        
        $userLevel = $roleHierarchy[$userRoleName] ?? 0;
        $creatorLevel = $roleHierarchy[$creatorRoleName] ?? 0;

        return $userLevel > $creatorLevel;
    }

    /**
     * Check if user can manage (update/delete) events of subordinate users.
     * 
     * Uses the same hierarchy logic as view permissions for consistency.
     *
     * @param User  $user  The authenticated user
     * @param Event $event The event to check management access for
     * @return bool True if user can manage subordinate events
     */
    private function canManageSubordinateEvents(User $user, Event $event): bool
    {
        return $this->canViewSubordinateEvents($user, $event);
    }
}