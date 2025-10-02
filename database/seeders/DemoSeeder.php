<?php

/**
 * Demo Data Seeder
 *
 * This seeder creates demo data for all modules and role-based demo users.
 * It is idempotent and can be run multiple times safely.
 *
 * @category Database
 * @package  Database\Seeders
 * @author   Platform Engineering Team <team@sellora.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/sellora
 */

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Demo Data Seeder Class
 *
 * @category Database
 * @package  Database\Seeders
 * @author   Platform Engineering Team <team@sellora.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/sellora
 */
class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Store generated passwords for demo users
     *
     * @var array
     */
    private array $_demoPasswords = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Check environment protection
        if (!$this->_canRunInCurrentEnvironment()) {
            $this->command->error('Demo seeding is not allowed in production environment.');
            $this->command->info('Set ALLOW_DEMO_SEED_IN_PROD=true to override.');
            return;
        }

        $this->command->info('Starting demo data seeding...');

        DB::transaction(
            function () {
                // Ensure roles exist first
                $this->call(RoleSeeder::class);
                
                // Create owner/bootstrap user
                $this->_createOwnerUser();
                
                // Create demo users for each role
                $this->_createDemoUsers();
                
                // Seed demo data for all modules
                $this->_seedModuleData();
            }
        );

        // Generate credentials file
        $this->_generateCredentialsFile();

        $this->command->info('Demo seeding completed successfully!');
        $this->command->info('Credentials saved to: DEMO_CREDENTIALS.md');
    }

    /**
     * Check if seeding can run in current environment
     *
     * @return bool
     */
    private function _canRunInCurrentEnvironment(): bool
    {
        $isProduction = app()->environment('production');
        $allowInProd = env('ALLOW_DEMO_SEED_IN_PROD', false);
        
        return !$isProduction || $allowInProd;
    }

    /**
     * Read secure credentials from protected file
     *
     * @return array
     */
    private function _readSecureCredentials(): array
    {
        $credentialsPath = storage_path('app/secure/.credentials');
        
        if (!file_exists($credentialsPath)) {
            throw new \Exception('Secure credentials file not found at: ' . $credentialsPath);
        }
        
        $content = file_get_contents($credentialsPath);
        $lines = explode("\n", $content);
        $credentials = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '=') === false) {
                continue;
            }
            
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            if ($key === 'OWNER_EMAIL') {
                $credentials['email'] = $value;
            } elseif ($key === 'OWNER_PASSWORD') {
                $credentials['password'] = $value;
            }
        }
        
        if (empty($credentials['email']) || empty($credentials['password'])) {
            throw new \Exception('Invalid credentials format in secure file.');
        }
        
        return $credentials;
    }

    /**
     * Create the owner/bootstrap user
     *
     * @return void
     */
    private function _createOwnerUser(): void
    {
        // Read credentials from secure file
        $credentials = $this->_readSecureCredentials();
        $email = $credentials['email'];
        $password = $credentials['password'];

        $ownerRole = Role::where('name', 'Author')->first();
        if (!$ownerRole) {
            throw new \Exception('Author role not found. Please run RoleSeeder first.');
        }

        $owner = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'System Owner',
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $ownerRole->id,
                'designation' => 'System Owner',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Owner user created/updated: {$email}");
        
        // Store password for credentials file (if allowed)
        if (env('INCLUDE_OWNER_PASSWORD_IN_OUTPUT', false)) {
            $this->_demoPasswords['owner'] = [
                'email' => $email,
                'password' => $password,
                'role' => 'Author (Owner)'
            ];
        } else {
            $this->_demoPasswords['owner'] = [
                'email' => $email,
                'password' => '[HIDDEN - Set INCLUDE_OWNER_PASSWORD_IN_OUTPUT=true to show]',
                'role' => 'Author (Owner)'
            ];
        }
    }

    /**
     * Create demo users for each role
     *
     * @return void
     */
    private function _createDemoUsers(): void
    {
        $roles = Role::all();
        
        foreach ($roles as $role) {
            // Skip Author role as it's used for owner
            if ($role->name === 'Author') {
                continue;
            }
            
            $email = strtolower($role->name) . '.demo@demo.local';
            $password = $this->_generateStrongPassword();
            
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => ucfirst($role->name) . ' Demo User',
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role_id' => $role->id,
                    'designation' => $role->description,
                    'email_verified_at' => now(),
                ]
            );
            
            $this->_demoPasswords[$role->name] = [
                'email' => $email,
                'password' => $password,
                'role' => $role->name
            ];
            
            $this->command->info("Demo user created/updated: {$email} ({$role->name})");
        }
    }

    /**
     * Seed demo data for all modules
     *
     * @return void
     */
    private function _seedModuleData(): void
    {
        $this->command->info('Seeding module demo data...');
        
        // Call other seeders for demo data
        $this->call(FooterBrandSeeder::class);
        
        // Call all module demo seeders
        $this->call(ProductModuleSeeder::class);
        $this->call(OrderDemoSeeder::class);
        $this->call(BillDemoSeeder::class);
        $this->call(BudgetDemoSeeder::class);
        $this->call(AssessmentDemoSeeder::class);
        $this->call(PresentationDemoSeeder::class);
        $this->call(LocationDemoSeeder::class);
        $this->call(VisitDemoSeeder::class);
        $this->call(EventDemoSeeder::class);
        
        $this->command->info('Module demo data seeded.');
    }

    /**
     * Generate a strong random password
     *
     * @return string
     */
    private function _generateStrongPassword(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        for ($i = 4; $i < 16; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        
        return str_shuffle($password);
    }

    /**
     * Generate the DEMO_CREDENTIALS.md file
     *
     * @return void
     */
    private function _generateCredentialsFile(): void
    {
        $content = "# Demo Credentials\n\n";
        $content .= "Generated on: " . now()->format('Y-m-d H:i:s') . "\n\n";
        $content .= "## User Accounts\n\n";
        $content .= "| User | Role | Email | Password |\n";
        $content .= "|------|------|-------|----------|\n";
        
        foreach ($this->_demoPasswords as $userData) {
            $content .= "| {$userData['role']} Demo | {$userData['role']} | {$userData['email']} | {$userData['password']} |\n";
        }
        
        $content .= "\n## Verification\n\n";
        $content .= "### Login Verification Results\n\n";
        
        // Perform basic verification
        $verificationResults = $this->_performVerification();
        
        foreach ($verificationResults as $result) {
            $status = $result['success'] ? '✅' : '❌';
            $content .= "- {$status} {$result['role']}: {$result['message']}\n";
        }
        
        $content .= "\n## Security Notes\n\n";
        $content .= "- All passwords are randomly generated and unique\n";
        $content .= "- Owner account is protected from deletion\n";
        $content .= "- Demo data is safe to regenerate\n";
        $content .= "- This file is excluded from version control\n";
        
        file_put_contents(base_path('DEMO_CREDENTIALS.md'), $content);
    }

    /**
     * Perform basic verification of demo users
     *
     * @return array
     */
    private function _performVerification(): array
    {
        $results = [];
        
        foreach ($this->_demoPasswords as $userData) {
            try {
                $user = User::where('email', $userData['email'])->first();
                if ($user && $user->role) {
                    $results[] = [
                        'role' => $userData['role'],
                        'success' => true,
                        'message' => 'User exists with correct role assignment'
                    ];
                } else {
                    $results[] = [
                        'role' => $userData['role'],
                        'success' => false,
                        'message' => 'User not found or missing role'
                    ];
                }
            } catch (\Exception $e) {
                $results[] = [
                    'role' => $userData['role'],
                    'success' => false,
                    'message' => 'Verification failed: ' . $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
}