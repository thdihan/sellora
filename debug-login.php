<?php
/**
 * Login Debug Helper
 * Place this in your public directory to debug login issues
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Login Debug Information</h1>";
echo "<hr>";

// Check session functionality
session_start();
echo "<p><strong>PHP Sessions:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? 'WORKING' : 'FAILED') . "</p>";

// Check Laravel directory structure
$laravelPath = dirname(__DIR__); // Assuming this is in public directory
echo "<p><strong>Laravel Path:</strong> $laravelPath</p>";

// Check critical directories
$directories = [
    'storage' => $laravelPath . '/storage',
    'storage/app' => $laravelPath . '/storage/app',
    'storage/framework' => $laravelPath . '/storage/framework',
    'storage/framework/sessions' => $laravelPath . '/storage/framework/sessions',
    'storage/framework/cache' => $laravelPath . '/storage/framework/cache',
    'storage/framework/views' => $laravelPath . '/storage/framework/views',
    'storage/logs' => $laravelPath . '/storage/logs',
    'bootstrap/cache' => $laravelPath . '/bootstrap/cache'
];

echo "<h3>Directory Check:</h3>";
foreach ($directories as $name => $path) {
    $exists = is_dir($path);
    $writable = $exists ? is_writable($path) : false;
    $status = $exists ? ($writable ? 'EXISTS & WRITABLE' : 'EXISTS BUT NOT WRITABLE') : 'NOT FOUND';
    $color = $writable ? 'green' : ($exists ? 'orange' : 'red');
    echo "<p><strong>{$name}:</strong> <span style='color: {$color}'>{$status}</span></p>";
}

// Check .env file
$envPath = $laravelPath . '/.env';
$envExists = file_exists($envPath);
echo "<p><strong>.env File:</strong> " . ($envExists ? 'EXISTS' : 'NOT FOUND') . "</p>";

if ($envExists) {
    // Parse .env and check database connection
    $env = [];
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
    
    echo "<h3>Environment Check:</h3>";
    echo "<p><strong>APP_KEY:</strong> " . (isset($env['APP_KEY']) && !empty($env['APP_KEY']) ? 'SET' : 'MISSING') . "</p>";
    echo "<p><strong>SESSION_DRIVER:</strong> " . ($env['SESSION_DRIVER'] ?? 'NOT SET') . "</p>";
    echo "<p><strong>CACHE_STORE:</strong> " . ($env['CACHE_STORE'] ?? 'NOT SET') . "</p>";
    echo "<p><strong>QUEUE_CONNECTION:</strong> " . ($env['QUEUE_CONNECTION'] ?? 'NOT SET') . "</p>";
    
    // Test database connection
    if (isset($env['DB_HOST'], $env['DB_DATABASE'], $env['DB_USERNAME'], $env['DB_PASSWORD'])) {
        try {
            $pdo = new PDO(
                "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']}", 
                $env['DB_USERNAME'], 
                $env['DB_PASSWORD'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "<p><strong>Database Connection:</strong> <span style='color: green'>SUCCESS</span></p>";
            
            // Check if users table exists
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            $usersTableExists = $stmt->rowCount() > 0;
            echo "<p><strong>Users Table:</strong> " . ($usersTableExists ? 'EXISTS' : 'NOT FOUND') . "</p>";
            
            // Check if sessions table exists (if using database sessions)
            if ($env['SESSION_DRIVER'] === 'database') {
                $stmt = $pdo->query("SHOW TABLES LIKE 'sessions'");
                $sessionsTableExists = $stmt->rowCount() > 0;
                echo "<p><strong>Sessions Table:</strong> " . ($sessionsTableExists ? 'EXISTS' : 'NOT FOUND') . "</p>";
            }
            
        } catch (Exception $e) {
            echo "<p><strong>Database Connection:</strong> <span style='color: red'>FAILED - " . $e->getMessage() . "</span></p>";
        }
    }
}

echo "<hr>";
echo "<p><em>Remove this file after debugging!</em></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1, h3 { color: #333; }
p { margin: 5px 0; }
</style>
