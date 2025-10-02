<?php

/**
 * Console routes for scheduled commands and artisan commands.
 *
 * PHP version 8.0
 *
 * @category Routes
 * @package  Routes
 * @author   Sellora Team <team@sellora.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://sellora.com
 */

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command(
    'inspire',
    function () {
        $this->comment(Inspiring::quote());
    }
)->purpose('Display an inspiring quote');

// Email notification scheduler
Schedule::command('notifications:send-due')
    ->everyMinute()
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping();

Schedule::command('notifications:build-daily-digest')
    ->dailyAt('10:00')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping();

// Sales target redistribution scheduler
Schedule::command('targets:redistribute-scheduled')
    ->dailyAt('23:30')
    ->timezone('Asia/Dhaka')
    ->withoutOverlapping();
