<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if bills already exist
        if (Bill::count() > 0) {
            $this->command->info('Bills already exist, skipping...');
            return;
        }

        $this->command->info('Creating demo bills...');

        DB::transaction(function () {
            // Get existing users for realistic data
            $users = User::all();
            
            if ($users->isEmpty()) {
                $this->command->warn('No users found, creating bills with factory users');
                Bill::factory(30)->create();
            } else {
                // Create bills for existing users
                foreach ($users->take(8) as $user) {
                    Bill::factory(rand(1, 5))->create(['user_id' => $user->id]);
                }
                
                // Create some additional random bills
                Bill::factory(15)->create();
            }

            // Create some specific bill states
            Bill::factory(5)->pending()->create();
            Bill::factory(3)->approved()->create();
            Bill::factory(2)->rejected()->create();
            Bill::factory(4)->highPriority()->create();
        });

        $this->command->info('Demo bills created successfully!');
    }
}