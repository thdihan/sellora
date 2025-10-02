<?php

/**
 * Artisan command to redistribute unfilled weekly and unachieved monthly sales targets
 *
 * @category Console
 * @package  App\Console\Commands
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesTarget;
use Carbon\Carbon;

/**
 * Command to redistribute sales targets automatically
 *
 * @category Console
 * @package  App\Console\Commands
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */
class RedistributeSalesTargets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales-targets:redistribute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redistribute unfilled weekly targets and unachieved monthly targets';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting sales targets redistribution...');
        
        $currentDate = Carbon::now();
        $currentYear = $currentDate->year;
        
        // Get all active sales targets for current year
        $salesTargets = SalesTarget::where('target_year', $currentYear)
            ->where('status', 'active')
            ->get();
        
        foreach ($salesTargets as $target) {
            $this->_redistributeWeeklyTargets($target, $currentDate);
            $this->_redistributeMonthlyTargets($target, $currentDate);
        }
        
        $this->info('Sales targets redistribution completed successfully!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Redistribute unfilled weekly targets to next week
     *
     * @param  SalesTarget  $salesTarget  The sales target to redistribute
     * @param  Carbon       $currentDate  Current date for calculations
     *
     * @return void
     */
    private function _redistributeWeeklyTargets($salesTarget, $currentDate)
    {
        $currentWeek = $currentDate->weekOfYear;
        $currentMonth = $currentDate->month;
        
        // Only redistribute if we're past the week
        if ($currentWeek > 1) {
            $salesTarget->redistributeWeeklyTargets($currentDate);
            $this->line("Redistributed weekly targets for user {$salesTarget->user_id}");
        }
    }
    
    /**
     * Redistribute unachieved monthly targets to next month
     *
     * @param  SalesTarget  $salesTarget  The sales target to redistribute
     * @param  Carbon       $currentDate  Current date for calculations
     *
     * @return void
     */
    private function _redistributeMonthlyTargets($salesTarget, $currentDate)
    {
        $currentMonth = $currentDate->month;
        
        // Only redistribute if we're past the month
        if ($currentMonth > 1) {
            $salesTarget->redistributeMonthlyTargets($currentDate);
            $this->line("Redistributed monthly targets for user {$salesTarget->user_id}");
        }
    }
}