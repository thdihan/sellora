<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if locations already exist
        if (Location::count() > 0) {
            $this->command->info('Locations already exist, skipping...');
            return;
        }

        $this->command->info('Creating demo locations...');

        DB::transaction(function () {
            // Get existing users for realistic data
            $users = User::all();
            
            if ($users->isEmpty()) {
                $this->command->warn('No users found, creating locations with factory users');
                Location::factory(15)->create();
            } else {
                // Create locations for existing users
                foreach ($users->take(5) as $user) {
                    Location::factory(rand(1, 3))->create(['user_id' => $user->id]);
                }
                
                // Create some additional random locations
                Location::factory(8)->create();
            }

            // Create some specific location states
            Location::factory(3)->office()->create();
            Location::factory(4)->client()->create();
            Location::factory(5)->active()->create();
        });

        $this->command->info('Demo locations created successfully!');
    }
}