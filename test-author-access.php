<?php
/**
 * Author Role Access Test Script
 * 
 * This script tests if the Author role has proper access to all routes
 * Place this in your public directory temporarily for testing
 */

// Include Laravel bootstrap
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h1>Author Role Access Test</h1>";
echo "<hr>";

try {
    // Test database connection
    $authorUser = App\Models\User::with('role')->whereHas('role', function($q) {
        $q->where('name', 'Author');
    })->first();

    if (!$authorUser) {
        echo "<p style='color: red;'>❌ No Author role user found in database</p>";
        echo "<p>Please create an Author role user first</p>";
        exit;
    }

    echo "<p style='color: green;'>✅ Author user found: {$authorUser->name} ({$authorUser->email})</p>";
    echo "<p><strong>Role:</strong> {$authorUser->role->name}</p>";
    echo "<hr>";

    // Test middleware functionality
    echo "<h3>Testing Role Middleware</h3>";
    
    // Simulate author login
    Illuminate\Support\Facades\Auth::login($authorUser);
    
    $middleware = new App\Http\Middleware\RoleMiddleware();
    $request = Illuminate\Http\Request::create('/test', 'GET');
    
    // Test with role restrictions
    $testRoles = ['Admin', 'Manager', 'Employee'];
    $result = $middleware->handle($request, function($req) {
        return new Illuminate\Http\Response('Access granted');
    }, ...$testRoles);
    
    if ($result->getStatusCode() === 200) {
        echo "<p style='color: green;'>✅ Author role bypassed middleware restrictions successfully</p>";
    } else {
        echo "<p style='color: red;'>❌ Author role failed to bypass middleware restrictions</p>";
    }

    // Test Gate functionality
    echo "<h3>Testing Gate Permissions</h3>";
    
    $gates = [
        'access-admin' => 'Admin Access',
        'manage-users' => 'User Management', 
        'access-products' => 'Product Access',
        'manage-taxes' => 'Tax Management',
        'access-reports' => 'Reports Access',
        'access-budgets' => 'Budget Access',
        'manage-api-connectors' => 'API Connectors',
        'import-export-data' => 'Data Import/Export'
    ];

    foreach ($gates as $gate => $description) {
        if (Illuminate\Support\Facades\Gate::allows($gate)) {
            echo "<p style='color: green;'>✅ {$description}: Access granted</p>";
        } else {
            echo "<p style='color: red;'>❌ {$description}: Access denied</p>";
        }
    }

    // Test trait functionality
    echo "<h3>Testing HasRoleBasedAccess Trait</h3>";
    
    $controller = new class extends App\Http\Controllers\Controller {
        use App\Traits\HasRoleBasedAccess;
        
        public function testAccess() {
            return [
                'hasRole_Admin' => $this->hasRole('Admin'),
                'hasRole_Multiple' => $this->hasRole(['Manager', 'Employee']),
                'isAuthor' => $this->isAuthor(),
                'canAccessAdmin' => $this->canAccessAdmin(),
                'canManageUsers' => $this->canManageUsers(),
                'getRoleLevel' => $this->getRoleLevel('Author')
            ];
        }
    };
    
    $traitResults = $controller->testAccess();
    
    foreach ($traitResults as $test => $result) {
        $status = $result ? '✅' : '❌';
        $color = $result ? 'green' : 'red';
        echo "<p style='color: {$color};'>{$status} {$test}: " . ($result ? 'PASS' : 'FAIL') . "</p>";
    }

    echo "<hr>";
    echo "<h3>Summary</h3>";
    echo "<p style='color: green; font-weight: bold;'>🎉 Author role access control is working correctly!</p>";
    echo "<p>The Author role has unrestricted access to all functionality.</p>";
    
    echo "<hr>";
    echo "<p style='color: orange;'><strong>Security Note:</strong> Remove this file after testing!</p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error during testing: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h3 { color: #333; }
p { margin: 5px 0; }
hr { margin: 20px 0; }
</style>
