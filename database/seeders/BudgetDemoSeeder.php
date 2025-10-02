<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BudgetDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if budgets already exist
        if (Budget::count() > 0) {
            $this->command->info('Budgets already exist, skipping...');
            return;
        }

        $this->command->info('Creating demo budgets...');

        DB::transaction(function () {
            // Get existing users for realistic data
            $users = User::all();
            
            if ($users->isEmpty()) {
                $this->command->warn('No users found, creating budgets with factory users');
                Budget::factory(20)->create();
            } else {
                // Create budgets for existing users
                foreach ($users->take(6) as $user) {
                    Budget::factory(rand(1, 3))->create(['created_by' => $user->id]);
                }
                
                // Create some additional random budgets
                Budget::factory(10)->create();
            }

            // Create some specific budget states
            Budget::factory(3)->active()->create();
            Budget::factory(2)->draft()->create();
            Budget::factory(4)->recurring()->create();
            Budget::factory(2)->highValue()->create();
        });

        $this->command->info('Demo budgets created successfully!');
    }
}