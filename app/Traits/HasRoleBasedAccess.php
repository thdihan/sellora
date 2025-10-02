<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Role-based Access Control Trait
 * 
 * Provides helper methods for implementing role-based access control
 * with special handling for Author role (super admin).
 */
trait HasRoleBasedAccess
{
    /**
     * Check if current user has any of the specified roles
     * Author role always returns true (super admin access)
     *
     * @param array|string $roles
     * @return bool
     */
    protected function hasRole($roles): bool
    {
        if (!Auth::check() || !Auth::user()->role) {
            return false;
        }

        $userRole = Auth::user()->role->name;

        // Author role has unrestricted access
        if ($userRole === 'Author') {
            return true;
        }

        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($userRole, $roles);
    }

    /**
     * Ensure user has required role or abort with 403
     * Author role always passes (super admin access)
     *
     * @param array|string $roles
     * @param string $message
     * @return void
     */
    protected function requireRole($roles, string $message = 'Insufficient permissions'): void
    {
        if (!$this->hasRole($roles)) {
            $userRole = Auth::user()->role->name ?? 'No Role';
            $requiredRoles = is_array($roles) ? implode(', ', $roles) : $roles;
            
            abort(403, "{$message}. Required: {$requiredRoles}. Your role: {$userRole}");
        }
    }

    /**
     * Check if current user is Author (super admin)
     *
     * @return bool
     */
    protected function isAuthor(): bool
    {
        return Auth::check() && 
               Auth::user()->role && 
               Auth::user()->role->name === 'Author';
    }

    /**
     * Check if current user can access admin functions
     * (Author or Admin roles)
     *
     * @return bool
     */
    protected function canAccessAdmin(): bool
    {
        return $this->hasRole(['Author', 'Admin']);
    }

    /**
     * Check if current user can manage users
     * (Author role only - strict permission)
     *
     * @return bool
     */
    protected function canManageUsers(): bool
    {
        return $this->hasRole(['Author']);
    }

    /**
     * Get role hierarchy level for comparison
     * Higher number = higher authority
     *
     * @param string $role
     * @return int
     */
    protected function getRoleLevel(string $role): int
    {
        $hierarchy = [
            'Author' => 100,      // Super admin - highest level
            'Admin' => 90,        // System admin
            'Chairman' => 80,     // Company leadership
            'Director' => 75,
            'ED' => 70,
            'GM' => 65,
            'DGM' => 60,
            'AGM' => 55,
            'NSM' => 50,          // Management levels
            'ZSM' => 40,
            'RSM' => 35,
            'ASM' => 30,
            'MPO' => 20,          // Field levels
            'MR' => 10,
            'Trainee' => 5        // Lowest level
        ];

        return $hierarchy[$role] ?? 0;
    }

    /**
     * Check if current user outranks another user
     *
     * @param string $otherUserRole
     * @return bool
     */
    protected function outranks(string $otherUserRole): bool
    {
        if (!Auth::check() || !Auth::user()->role) {
            return false;
        }

        $currentUserLevel = $this->getRoleLevel(Auth::user()->role->name);
        $otherUserLevel = $this->getRoleLevel($otherUserRole);

        return $currentUserLevel > $otherUserLevel;
    }
}
