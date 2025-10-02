<?php

// Check roles and create Author user if needed
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

echo "=== CHECKING ROLES AND USERS ===\n\n";

// Check existing roles
$roles = Role::all();
echo "Existing roles:\n";
foreach ($roles as $role) {
    $userCount = User::where('role_id', $role->id)->count();
    echo "- {$role->name} (ID: {$role->id}) - {$userCount} users\n";
}

// Check if Author role exists
$authorRole = Role::where('name', 'Author')->first();
if (!$authorRole) {
    echo "\n❌ Author role not found. Creating Author role...\n";
    $authorRole = Role::create([
        'name' => 'Author',
        'description' => 'Super Admin role with unrestricted access to all features'
    ]);
    echo "✅ Author role created (ID: {$authorRole->id})\n";
} else {
    echo "\n✅ Author role exists (ID: {$authorRole->id})\n";
}

// Check if Author user exists
$authorUser = User::where('role_id', $authorRole->id)->first();
if (!$authorUser) {
    echo "\n❌ No Author user found. Creating Author user...\n";
    
    // Create Author user
    $authorUser = User::create([
        'name' => 'System Author',
        'email' => 'author@sellora.com',
        'password' => Hash::make('author123'), // Change this password!
        'role_id' => $authorRole->id,
        'email_verified_at' => now(),
        'is_active' => true
    ]);
    
    echo "✅ Author user created:\n";
    echo "   Email: author@sellora.com\n";
    echo "   Password: author123 (Please change this!)\n";
    echo "   ID: {$authorUser->id}\n";
} else {
    echo "\n✅ Author user exists:\n";
    echo "   Name: {$authorUser->name}\n";
    echo "   Email: {$authorUser->email}\n";
    echo "   ID: {$authorUser->id}\n";
}

echo "\n=== SUMMARY ===\n";
echo "✅ Author role: Ready\n";
echo "✅ Author user: Ready\n";
echo "\nYou can now:\n";
echo "1. Login with email: {$authorUser->email}\n";
echo "2. Test access with: php test-author-complete-access.php\n";
echo "3. Access all routes and features without restrictions\n";

echo "\nSetup completed! 🎉\n";
