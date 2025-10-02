<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\GlobalAccessPolicy;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Register model policies here if needed
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Global gate definitions with Author role having unrestricted access
        Gate::before(function (User $user, string $ability) {
            // Author role has unrestricted access to everything
            if ($user->role && $user->role->name === 'Author') {
                return true;
            }
            
            // Let specific gates handle other permissions
            return null;
        });

        // Define specific gates for different access levels
        Gate::define('access-admin', function (User $user) {
            return $user->role && in_array($user->role->name, ['Author', 'Admin']);
        });

        Gate::define('manage-users', function (User $user) {
            return $user->role && $user->role->name === 'Author';
        });

        Gate::define('access-products', function (User $user) {
            return $user->role && in_array($user->role->name, ['Author', 'Admin']);
        });

        Gate::define('manage-taxes', function (User $user) {
            return $user->role && $user->role->name === 'Author';
        });

        Gate::define('access-reports', function (User $user) {
            return $user->role && in_array($user->role->name, [
                'Author', 'Admin', 'Chairman', 'Director', 'ED', 'GM', 'DGM', 'AGM', 'NSM'
            ]);
        });

        Gate::define('access-budgets', function (User $user) {
            return $user->role && in_array($user->role->name, [
                'Author', 'Admin', 'Chairman', 'Director', 'ED', 'GM', 'DGM', 'AGM', 'NSM', 'ZSM'
            ]);
        });

        Gate::define('access-location-tracking', function (User $user) {
            return $user->role && in_array($user->role->name, [
                'Author', 'Admin', 'Chairman', 'Director', 'ED', 'GM', 'DGM', 'AGM', 'NSM', 'ZSM', 'RSM', 'ASM'
            ]);
        });

        Gate::define('manage-api-connectors', function (User $user) {
            return $user->role && $user->role->name === 'Author';
        });

        Gate::define('import-export-data', function (User $user) {
            return $user->role && $user->role->name === 'Author';
        });

        // Role hierarchy checking
        Gate::define('outranks', function (User $user, string $targetRole) {
            if (!$user->role) {
                return false;
            }

            $hierarchy = [
                'Author' => 100,
                'Admin' => 90,
                'Chairman' => 80,
                'Director' => 75,
                'ED' => 70,
                'GM' => 65,
                'DGM' => 60,
                'AGM' => 55,
                'NSM' => 50,
                'ZSM' => 40,
                'RSM' => 35,
                'ASM' => 30,
                'MPO' => 20,
                'MR' => 10,
                'Trainee' => 5
            ];

            $userLevel = $hierarchy[$user->role->name] ?? 0;
            $targetLevel = $hierarchy[$targetRole] ?? 0;

            return $userLevel > $targetLevel;
        });
    }
}
