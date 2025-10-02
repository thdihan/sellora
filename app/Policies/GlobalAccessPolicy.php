<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Global Access Policy
 * 
 * Provides universal access control with Author role having unrestricted access
 */
class GlobalAccessPolicy
{
    use HandlesAuthorization;

    /**
     * Check if user has global admin access (Author role)
     *
     * @param User $user
     * @return bool
     */
    public function before(User $user, string $ability): bool|null
    {
        // Author role has unrestricted access to everything
        if ($user->role && $user->role->name === 'Author') {
            return true;
        }

        // Let other policies handle specific permissions
        return null;
    }

    /**
     * Check if user can access admin functions
     *
     * @param User $user
     * @return bool
     */
    public function accessAdmin(User $user): bool
    {
        return $user->role && in_array($user->role->name, ['Author', 'Admin']);
    }

    /**
     * Check if user can manage users
     *
     * @param User $user
     * @return bool
     */
    public function manageUsers(User $user): bool
    {
        return $user->role && $user->role->name === 'Author';
    }

    /**
     * Check if user can access products
     *
     * @param User $user
     * @return bool
     */
    public function accessProducts(User $user): bool
    {
        return $user->role && in_array($user->role->name, ['Author', 'Admin']);
    }

    /**
     * Check if user can access reports
     *
     * @param User $user
     * @return bool
     */
    public function accessReports(User $user): bool
    {
        return $user->role && in_array($user->role->name, [
            'Author', 'Admin', 'Chairman', 'Director', 'ED', 'GM', 'DGM', 'AGM', 'NSM'
        ]);
    }

    /**
     * Check if user can access budgets
     *
     * @param User $user
     * @return bool
     */
    public function accessBudgets(User $user): bool
    {
        return $user->role && in_array($user->role->name, [
            'Author', 'Admin', 'Chairman', 'Director', 'ED', 'GM', 'DGM', 'AGM', 'NSM', 'ZSM'
        ]);
    }

    /**
     * Check if user can access location tracking
     *
     * @param User $user
     * @return bool
     */
    public function accessLocationTracking(User $user): bool
    {
        return $user->role && in_array($user->role->name, [
            'Author', 'Admin', 'Chairman', 'Director', 'ED', 'GM', 'DGM', 'AGM', 'NSM', 'ZSM', 'RSM', 'ASM'
        ]);
    }

    /**
     * Check if user can access tax management
     *
     * @param User $user
     * @return bool
     */
    public function manageTaxes(User $user): bool
    {
        return $user->role && $user->role->name === 'Author';
    }

    /**
     * Check if user can access API connectors
     *
     * @param User $user
     * @return bool
     */
    public function manageApiConnectors(User $user): bool
    {
        return $user->role && $user->role->name === 'Author';
    }

    /**
     * Check if user can import/export data
     *
     * @param User $user
     * @return bool
     */
    public function importExportData(User $user): bool
    {
        return $user->role && $user->role->name === 'Author';
    }
}
