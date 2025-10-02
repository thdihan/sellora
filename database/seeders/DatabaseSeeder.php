<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call([
            RoleSeeder::class,
        ]);

        // Create default admin user
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@sellora.com',
            'role_id' => $adminRole?->id,
        ]);

        // Seed tax settings, tax heads, enhanced product data, and settings
        $this->call([
            TaxSettingsSeeder::class,
            TaxHeadSeeder::class,
            EnhancedProductSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
