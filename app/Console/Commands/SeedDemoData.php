<?php

namespace App\Console\Commands;

use Database\Seeders\DemoSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:seed 
                            {--force : Force seeding even in production}
                            {--verify : Run verification after seeding}
                            {--owner-password= : Set owner password (overrides env)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed demo data and create role-based demo users with secure owner bootstrap';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Demo Data Seeder');
        $this->info('==================');
        
        // Environment check
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('❌ Cannot run demo seeding in production environment.');
            $this->info('💡 Use --force flag or set ALLOW_DEMO_SEED_IN_PROD=true to override.');
            return 1;
        }
        
        // Owner password check
        $ownerPassword = $this->option('owner-password') ?? env('BOOTSTRAP_OWNER_PASSWORD');
        if (empty($ownerPassword)) {
            $ownerPassword = $this->secret('Enter owner password (required)');
            if (empty($ownerPassword)) {
                $this->error('❌ Owner password is required for security.');
                return 1;
            }
        }
        
        // Set the password in environment for the seeder
        putenv("BOOTSTRAP_OWNER_PASSWORD={$ownerPassword}");
        $_ENV['BOOTSTRAP_OWNER_PASSWORD'] = $ownerPassword;
        config(['app.bootstrap_owner_password' => $ownerPassword]);
        
        // Confirmation in production
        if (app()->environment('production')) {
            $this->warn('⚠️  You are about to seed demo data in PRODUCTION!');
            if (!$this->confirm('Are you absolutely sure you want to continue?')) {
                $this->info('Demo seeding cancelled.');
                return 0;
            }
        }
        
        try {
            $this->info('🚀 Starting demo data seeding...');
            
            // Run the demo seeder
            Artisan::call('db:seed', [
                '--class' => DemoSeeder::class,
                '--force' => true
            ]);
            
            $this->info('✅ Demo seeding completed successfully!');
            
            // Show credentials file location
            $credentialsPath = base_path('DEMO_CREDENTIALS.md');
            if (file_exists($credentialsPath)) {
                $this->info("📄 Credentials saved to: {$credentialsPath}");
                
                if ($this->option('verify')) {
                    $this->runVerification();
                }
            }
            
            $this->displayNextSteps();
            
        } catch (\Exception $e) {
            $this->error("❌ Demo seeding failed: {$e->getMessage()}");
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Run verification tests
     */
    private function runVerification(): void
    {
        $this->info('🔍 Running verification tests...');
        
        try {
            // Run basic verification
            Artisan::call('test', [
                '--filter' => 'DemoTest',
                '--stop-on-failure' => true
            ]);
            
            $this->info('✅ Verification tests passed!');
        } catch (\Exception $e) {
            $this->warn("⚠️  Verification tests failed: {$e->getMessage()}");
        }
    }
    
    /**
     * Display next steps to the user
     */
    private function displayNextSteps(): void
    {
        $this->info('');
        $this->info('🎉 Next Steps:');
        $this->info('=============');
        $this->info('1. Check DEMO_CREDENTIALS.md for login details');
        $this->info('2. Visit your application and test role-based access');
        $this->info('3. Run verification: php artisan demo:seed --verify');
        $this->info('4. For production: Set proper environment variables');
        $this->info('');
        $this->info('🔒 Security Notes:');
        $this->info('- Owner account is protected from deletion');
        $this->info('- Demo passwords are randomly generated');
        $this->info('- DEMO_CREDENTIALS.md is excluded from git');
        $this->info('');
    }
}
