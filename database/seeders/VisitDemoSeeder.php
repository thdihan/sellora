<?php

namespace Database\Seeders;

use App\Models\Visit;
use App\Models\User;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VisitDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if visits already exist
        if (Visit::count() > 0) {
            $this->command->info('Visits already exist, skipping...');
            return;
        }

        $this->command->info('Creating demo visits...');

        DB::transaction(function () {
            // Ensure we have locations first
            if (Location::count() === 0) {
                $this->call(LocationDemoSeeder::class);
            }

            // Get existing users and locations for realistic data
            $users = User::all();
            $locations = Location::all();
            
            if ($users->isEmpty()) {
                $this->command->warn('No users found, creating visits with factory users');
                Visit::factory(30)->create();
            } else {
                // Create visits for existing users
                foreach ($users->take(8) as $user) {
                    Visit::factory(rand(2, 6))->create([
                        'user_id' => $user->id
                    ]);
                }
                
                // Create some additional random visits
                Visit::factory(15)->create();
            }

            // Create some specific visit states
            Visit::factory(5)->completed()->create();
            Visit::factory(3)->scheduled()->create();
            Visit::factory(4)->highPriority()->create();
        });

        $this->command->info('Demo visits created successfully!');
    }
}