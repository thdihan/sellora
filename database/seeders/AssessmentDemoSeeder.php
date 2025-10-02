<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssessmentDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if assessments already exist
        if (Assessment::count() > 0) {
            $this->command->info('Assessments already exist, skipping...');
            return;
        }

        $this->command->info('Creating demo assessments...');

        DB::transaction(function () {
            // Get existing users for realistic data
            $users = User::all();
            
            if ($users->isEmpty()) {
                $this->command->warn('No users found, creating assessments with factory users');
                Assessment::factory(15)->create();
            } else {
                // Create assessments for existing users
                foreach ($users->take(5) as $user) {
                    Assessment::factory(rand(1, 4))->create(['user_id' => $user->id]);
                }
                
                // Create some additional random assessments
                Assessment::factory(8)->create();
            }

            // Create some specific assessment states
            Assessment::factory(3)->active()->create();
            Assessment::factory(2)->inactive()->create();
            Assessment::factory(2)->certification()->create();
            Assessment::factory(3)->quickQuiz()->create();
        });

        $this->command->info('Demo assessments created successfully!');
    }
}