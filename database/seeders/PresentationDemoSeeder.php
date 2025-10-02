<?php

namespace Database\Seeders;

use App\Models\Presentation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentationDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if presentations already exist
        if (Presentation::count() > 0) {
            $this->command->info('Presentations already exist, skipping...');
            return;
        }

        $this->command->info('Creating demo presentations...');

        DB::transaction(function () {
            // Get existing users for realistic data
            $users = User::all();
            
            if ($users->isEmpty()) {
                $this->command->warn('No users found, creating presentations with factory users');
                Presentation::factory(25)->create();
            } else {
                // Create presentations for existing users
                foreach ($users->take(7) as $user) {
                    Presentation::factory(rand(2, 6))->create(['user_id' => $user->id]);
                }
                
                // Create some additional random presentations
                Presentation::factory(12)->create();
            }

            // Create some specific presentation states
            Presentation::factory(5)->public()->create();
            Presentation::factory(3)->featured()->create();
            Presentation::factory(4)->popular()->create();
        });

        $this->command->info('Demo presentations created successfully!');
    }
}