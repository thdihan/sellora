<?php

/**
 * Update User Credentials Command
 *
 * @category Commands
 * @package  App\Console\Commands
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Class UpdateUserCredentials
 *
 * Updates user emails to role@sellora.com format and sets common password
 *
 * @category Commands
 * @package  App\Console\Commands
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class UpdateUserCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-credentials {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user emails to role@sellora.com format and set common password for non-Author users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $commonPassword = 'Sellora@123';
        $hashedPassword = Hash::make($commonPassword);
        
        $this->info('Starting user credentials update...');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        // Get all users except Author role
        $users = User::with('role')
            ->whereHas(
                'role',
                function ($query) {
                    $query->where('name', '!=', 'Author');
                }
            )
            ->get();
            
        $this->info("Found {$users->count()} non-Author users to update");
        
        $updatedUsers = [];
        $errors = [];
        
        foreach ($users as $user) {
            try {
                $roleName = $user->role ? strtolower($user->role->name) : 'user';
                
                // Check if email already exists, add number suffix if needed
                $baseEmail = $roleName . '@sellora.com';
                $newEmail = $baseEmail;
                $counter = 1;
                
                while (User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
                    $counter++;
                    $newEmail = $roleName . $counter . '@sellora.com';
                }
                
                $this->line("Processing: {$user->name} (ID: {$user->id})");
                $this->line("  Current email: {$user->email}");
                $this->line("  New email: {$newEmail}");
                $this->line("  Role: {$user->role->name}");
                
                if (!$isDryRun) {
                    $user->update(
                        [
                            'email' => $newEmail,
                            'password' => $hashedPassword,
                            'email_verified_at' => now(), // Re-verify email
                        ]
                    );
                }
                
                $updatedUsers[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role->name,
                    'old_email' => $user->email,
                    'new_email' => $newEmail,
                    'password' => $commonPassword
                ];
                
                $this->info("  ✓ Updated successfully");
                
            } catch (\Exception $e) {
                $error = "Failed to update user {$user->name} (ID: {$user->id}): {$e->getMessage()}";
                $this->error($error);
                $errors[] = $error;
            }
            
            $this->line('');
        }
        
        // Generate credentials file
        if (!$isDryRun && !empty($updatedUsers)) {
            $this->_generateCredentialsFile($updatedUsers);
        }
        
        // Summary
        $this->info('=== SUMMARY ===');
        $this->info("Users processed: {$users->count()}");
        $this->info("Successfully updated: " . count($updatedUsers));
        
        if (!empty($errors)) {
            $this->error("Errors encountered: " . count($errors));
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }
        
        if ($isDryRun) {
            $this->warn('This was a dry run. To apply changes, run without --dry-run flag.');
        } else {
            $this->info('User credentials have been updated successfully!');
            $this->info('Credentials file generated: storage/app/user_credentials.txt');
        }
        
        return 0;
    }
    
    /**
     * Generate credentials file with all updated user information
     *
     * @param array $users Array of updated user data
     *
     * @return void
     */
    private function _generateCredentialsFile(array $users)
    {
        $content = "SELLORA SYSTEM - USER CREDENTIALS\n";
        $content .= "Generated on: " . now()->format('Y-m-d H:i:s') . "\n";
        $content .= "Common Password: Sellora@123\n";
        $content .= str_repeat('=', 60) . "\n\n";
        
        // Group users by role
        $usersByRole = collect($users)->groupBy('role');
        
        foreach ($usersByRole as $role => $roleUsers) {
            $content .= "ROLE: {$role}\n";
            $content .= str_repeat('-', 30) . "\n";
            
            foreach ($roleUsers as $user) {
                $content .= "Name: {$user['name']}\n";
                $content .= "Email: {$user['new_email']}\n";
                $content .= "Password: {$user['password']}\n";
                $content .= "User ID: {$user['id']}\n";
                $content .= "\n";
            }
            
            $content .= "\n";
        }
        
        // Add Author user info (unchanged)
        $authorUser = User::whereHas(
            'role',
            function ($query) {
                $query->where('name', 'Author');
            }
        )->first();
        
        if ($authorUser) {
            $content .= "ROLE: Author (System Owner - Unchanged)\n";
            $content .= str_repeat('-', 30) . "\n";
            $content .= "Name: {$authorUser->name}\n";
            $content .= "Email: {$authorUser->email}\n";
            $content .= "Password: [Original password maintained]\n";
            $content .= "User ID: {$authorUser->id}\n";
            $content .= "\n";
        }
        
        $content .= "\n" . str_repeat('=', 60) . "\n";
        $content .= "IMPORTANT NOTES:\n";
        $content .= "- All non-Author users now have the common password: Sellora@123\n";
        $content .= "- Email format: role@sellora.com (e.g., admin@sellora.com)\n";
        $content .= "- Author account remains unchanged for security\n";
        $content .= "- All emails have been re-verified automatically\n";
        
        Storage::put('user_credentials.txt', $content);
        
        $this->info('Credentials file saved to: storage/app/user_credentials.txt');
    }
}