<?php

/**
 * Scheduled command to automatically redistribute sales targets.
 *
 * PHP version 8.0
 *
 * @category Console
 * @package  App\Console\Commands
 * @author   Sellora Team <team@sellora.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://sellora.com
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesTarget;
use App\Models\User;
use Carbon\Carbon;

/**
 * Scheduled command to automatically redistribute sales targets.
 *
 * @category Console
 * @package  App\Console\Commands
 * @author   Sellora Team <team@sellora.com>
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     https://sellora.com
 */
class RedistributeTargetsScheduled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'targets:redistribute-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically redistribute unfilled weekly and unachieved monthly sales targets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Starting automatic target redistribution...');
        
        $currentDate = Carbon::now();
        $processedUsers = 0;
        
        // Get all users with sales targets assigned to them
        $userIds = SalesTarget::distinct()->pluck('assigned_to_user_id');
        $users = User::whereIn('id', $userIds)->get();
        
        foreach ($users as $user) {
            try {
                // Redistribute weekly targets
                SalesTarget::redistributeWeeklyTargets($user->id, $currentDate);
                
                // Redistribute monthly targets
                SalesTarget::redistributeMonthlyTargets($user->id, $currentDate);
                
                $processedUsers++;
                
                $this->line("Processed targets for user: {$user->name} (ID: {$user->id})");
                
            } catch (\Exception $e) {
                $this->error("Failed to process targets for user {$user->id}: {$e->getMessage()}");
            }
        }
        
        $this->info("Target redistribution completed. Processed {$processedUsers} users.");
        
        return Command::SUCCESS;
    }
}