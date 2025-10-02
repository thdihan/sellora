<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if orders already exist
        if (Order::count() > 0) {
            $this->command->info('Orders already exist, skipping...');
            return;
        }

        $this->command->info('Creating demo orders...');

        DB::transaction(function () {
            // Get existing users for realistic data
            $users = User::all();
            
            if ($users->isEmpty()) {
                $this->command->warn('No users found, creating orders with factory users');
                // Create orders with factory users
                Order::factory(50)->create();
            } else {
                // Create orders for existing users
                foreach ($users->take(10) as $user) {
                    Order::factory(rand(2, 8))->create([
                        'user_id' => $user->id
                    ]);
                }
                
                // Create some additional random orders
                Order::factory(20)->create();
            }

            // Create some specific order states
            Order::factory(5)->pending()->create();
            Order::factory(8)->completed()->create();
            Order::factory(3)->highValue()->create();
        });

        $this->command->info('Demo orders created successfully!');
    }
}