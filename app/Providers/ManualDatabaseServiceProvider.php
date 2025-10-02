<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Events\MigrationsStarted;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Schema\Builder;

class ManualDatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Disable migration checks when using manual database setup
        if (config('manual-database.manual_database_setup', false)) {
            $this->disableMigrationChecks();
        }

        // Set default string length for MySQL compatibility
        Builder::defaultStringLength(191);
    }

    /**
     * Disable automatic migration checks
     */
    protected function disableMigrationChecks(): void
    {
        // Skip migration status checks
        Event::listen(MigrationsStarted::class, function () {
            // Log that migrations are being bypassed
            logger('Manual database setup enabled - skipping migration checks');
        });

        Event::listen(MigrationsEnded::class, function () {
            // Log completion
            logger('Manual database setup - migration checks completed');
        });
    }
}
