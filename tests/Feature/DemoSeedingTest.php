<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoSeedingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set required environment variables for testing
        putenv('BOOTSTRAP_OWNER_EMAIL=test.owner@demo.local');
        putenv('BOOTSTRAP_OWNER_PASSWORD=TestOwner123!');
        putenv('ALLOW_DEMO_SEED_IN_PROD=true');
    }

    public function test_demo_seeder_creates_all_role_based_users(): void
    {
        // Run role seeder first
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RoleSeeder']);
        
        // Run demo seeder
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder']);
        
        // Verify owner user exists
        $owner = User::where('email', 'test.owner@demo.local')->first();
        $this->assertNotNull($owner);
        $this->assertEquals('System Owner', $owner->name);
        $this->assertTrue($owner->hasRole('Author'));
        
        // Verify demo users exist for each role (except Author)
        $roles = Role::where('name', '!=', 'Author')->get();
        
        foreach ($roles as $role) {
            $demoEmail = strtolower($role->name) . '.demo@demo.local';
            $demoUser = User::where('email', $demoEmail)->first();
            
            $this->assertNotNull($demoUser, "Demo user not found for role: {$role->name}");
            $this->assertEquals($role->id, $demoUser->role_id);
            $this->assertNotNull($demoUser->email_verified_at);
        }
    }

    public function test_demo_seeder_is_idempotent(): void
    {
        // Run role seeder first
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RoleSeeder']);
        
        // Run demo seeder twice
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder']);
        $firstRunUserCount = User::count();
        
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder']);
        $secondRunUserCount = User::count();
        
        // User count should be the same (no duplicates)
        $this->assertEquals($firstRunUserCount, $secondRunUserCount);
    }

    public function test_owner_account_protection(): void
    {
        // Run seeders
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RoleSeeder']);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder']);
        
        $owner = User::where('email', 'test.owner@demo.local')->first();
        $this->assertNotNull($owner);
        
        // Test deletion protection
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Owner account cannot be deleted');
        $owner->delete();
    }

    public function test_owner_role_change_protection(): void
    {
        // Run seeders
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RoleSeeder']);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder']);
        
        $owner = User::where('email', 'test.owner@demo.local')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        
        $this->assertNotNull($owner);
        $this->assertNotNull($adminRole);
        
        // Test role change protection
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Owner role cannot be changed');
        $owner->update(['role_id' => $adminRole->id]);
    }

    public function test_demo_users_can_authenticate(): void
    {
        // Run seeders
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RoleSeeder']);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder']);
        
        // Test a few demo users can be found and have proper attributes
        $testRoles = ['Admin', 'NSM', 'MR'];
        
        foreach ($testRoles as $roleName) {
            $demoEmail = strtolower($roleName) . '.demo@demo.local';
            $user = User::where('email', $demoEmail)->first();
            
            $this->assertNotNull($user, "Demo user not found: {$demoEmail}");
            $this->assertTrue($user->hasRole($roleName));
            $this->assertNotNull($user->password);
            $this->assertNotNull($user->email_verified_at);
        }
    }

    public function test_credentials_file_generation(): void
    {
        // Run seeders
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RoleSeeder']);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder']);
        
        $credentialsPath = base_path('DEMO_CREDENTIALS.md');
        $this->assertFileExists($credentialsPath);
        
        $content = file_get_contents($credentialsPath);
        $this->assertStringContainsString('Demo Credentials', $content);
        $this->assertStringContainsString('User Accounts', $content);
        $this->assertStringContainsString('Verification', $content);
        $this->assertStringContainsString('test.owner@demo.local', $content);
    }

    public function test_environment_protection(): void
    {
        // Remove the override
        putenv('ALLOW_DEMO_SEED_IN_PROD=false');
        
        // Mock production environment
        app()->detectEnvironment(function () {
            return 'production';
        });
        
        // Run role seeder first
        Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\RoleSeeder']);
        
        // Demo seeder should not run in production without override
        $exitCode = Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder']);
        
        // Should exit early without creating demo users
        $demoUserCount = User::where('email', 'like', '%.demo@demo.local')->count();
        $this->assertEquals(0, $demoUserCount);
    }

    protected function tearDown(): void
    {
        // Clean up credentials file
        $credentialsPath = base_path('DEMO_CREDENTIALS.md');
        if (file_exists($credentialsPath)) {
            unlink($credentialsPath);
        }
        
        parent::tearDown();
    }
}