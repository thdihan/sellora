<?php

/**
 * Event Demo Seeder
 *
 * This file contains the EventDemoSeeder class for seeding demo event data.
 *
 * @package Database\Seeders
 * @author  Sellora Team
 * @since   1.0.0
 */

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Event Demo Seeder Class
 *
 * Seeds the database with demo event data for testing and demonstration purposes.
 */
class EventDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Skip if events already exist
        if (Event::count() > 0) {
            $this->command->info('Events already exist, skipping...');
            return;
        }

        $this->command->info('Creating demo events...');

        DB::transaction(function () {
            // Get existing users for realistic data
            $users = User::all();
            
            if ($users->isEmpty()) {
                $this->command->warn('No users found, creating events with factory users');
                Event::factory(20)->create();
            } else {
                // Create events for existing users
                foreach ($users->take(5) as $user) {
                    Event::factory(rand(3, 8))->create(['created_by' => $user->id]);
                }
                
                // Create some additional random events
                Event::factory(15)->create();
            }

            // Create some specific event types
            $this->_createSpecificEvents($users->first());
        });

        $this->command->info('Demo events created successfully!');
    }

    /**
     * Create specific demo events with realistic data
     *
     * @param User|null $user The user to create events for
     * @return void
     */
    private function _createSpecificEvents($user)
    {
        $userId = $user ? $user->id : User::first()->id;
        
        // Team meeting
        Event::create([
            'title' => 'Weekly Team Meeting',
            'description' => 'Regular team sync to discuss project progress and upcoming tasks.',
            'event_type' => 'meeting',
            'start_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'start_time' => Carbon::now()->addDays(2)->setTime(10, 0)->format('Y-m-d H:i:s'),
            'end_time' => Carbon::now()->addDays(2)->setTime(11, 0)->format('Y-m-d H:i:s'),
            'location' => 'Conference Room A',
            'priority' => 'high',
            'status' => 'scheduled',
            'color' => '#007bff',
            'reminder_minutes' => 30,
            'attendees' => json_encode(['john@example.com', 'jane@example.com']),
            'created_by' => $userId,
            'recurring_type' => 'weekly',
            'recurring_end_date' => Carbon::now()->addMonths(3)->format('Y-m-d'),
        ]);

        // Client presentation
        Event::create([
            'title' => 'Client Presentation - Q4 Results',
            'description' => 'Present quarterly results and discuss future strategies with key clients.',
            'event_type' => 'meeting',
            'start_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'start_time' => Carbon::now()->addDays(5)->setTime(14, 0)->format('Y-m-d H:i:s'),
            'end_time' => Carbon::now()->addDays(5)->setTime(16, 0)->format('Y-m-d H:i:s'),
            'location' => 'Main Boardroom',
            'priority' => 'high',
            'status' => 'scheduled',
            'color' => '#28a745',
            'reminder_minutes' => 60,
            'attendees' => json_encode(['client@company.com', 'manager@sellora.com']),
            'created_by' => $userId,
        ]);

        // Project deadline
        Event::create([
            'title' => 'Project Alpha Deadline',
            'description' => 'Final deadline for Project Alpha deliverables.',
            'event_type' => 'deadline',
            'start_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'is_all_day' => true,
            'priority' => 'high',
            'status' => 'scheduled',
            'color' => '#dc3545',
            'reminder_minutes' => 1440, // 24 hours
            'created_by' => $userId,
        ]);

        // Personal reminder
        Event::create([
            'title' => 'Annual Health Checkup',
            'description' => 'Yearly medical checkup appointment.',
            'event_type' => 'personal',
            'start_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'start_time' => Carbon::now()->addDays(7)->setTime(9, 30)->format('Y-m-d H:i:s'),
            'end_time' => Carbon::now()->addDays(7)->setTime(10, 30)->format('Y-m-d H:i:s'),
            'location' => 'City Medical Center',
            'priority' => 'medium',
            'status' => 'scheduled',
            'color' => '#6f42c1',
            'reminder_minutes' => 120,
            'created_by' => $userId,
        ]);

        // Holiday
        Event::create([
            'title' => 'Independence Day',
            'description' => 'National holiday - office closed.',
            'event_type' => 'holiday',
            'start_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'is_all_day' => true,
            'priority' => 'low',
            'status' => 'scheduled',
            'color' => '#fd7e14',
            'created_by' => $userId,
        ]);
    }
}