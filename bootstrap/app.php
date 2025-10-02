<?php

/**
 * Laravel application bootstrap file.
 *
 * This file configures the Laravel application with routing, middleware,
 * and exception handling.
 *
 * @category  Application
 * @package   Laravel
 * @author    Laravel Team
 * @license   MIT License
 * @link      https://laravel.com
 */

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
