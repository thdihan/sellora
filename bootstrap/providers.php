<?php

/**
 * Application Service Providers
 *
 * This file returns an array of service providers that should be
 * automatically loaded by the Laravel application.
 *
 * @category Bootstrap
 * @package  Laravel
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\ManualDatabaseServiceProvider::class,
];
