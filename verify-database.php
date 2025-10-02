<?php

/**
 * Database Verification Script
 * 
 * This script verifies that the manual database setup is working correctly
 */

require_once 'vendor/autoload.php';

// Load environment variables
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $_ENV[trim($name)] = trim($value);
        }
    }
}

echo "Sellora Database Verification\n";
echo "============================\n\n";

try {
    // Database connection details
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? 3306;
    $database = $_ENV['DB_DATABASE'] ?? 'sellora_db';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';

    echo "Testing database connection...\n";
    echo "Host: $host:$port\n";
    echo "Database: $database\n";
    echo "Username: $username\n\n";

    // Create PDO connection
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "✓ Database connection successful!\n\n";

    // Check if tables exist
    echo "Checking database tables...\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $expectedTables = [
        'users', 'roles', 'products', 'orders', 'customers', 'bills',
        'product_categories', 'product_brands', 'warehouses', 'settings'
    ];

    $missingTables = [];
    foreach ($expectedTables as $table) {
        if (in_array($table, $tables)) {
            echo "✓ Table '$table' exists\n";
        } else {
            echo "✗ Table '$table' missing\n";
            $missingTables[] = $table;
        }
    }

    echo "\nTotal tables found: " . count($tables) . "\n";

    if (!empty($missingTables)) {
        echo "\n⚠️  Missing tables detected!\n";
        echo "Missing: " . implode(', ', $missingTables) . "\n";
        echo "Please import the database SQL files.\n\n";
    } else {
        echo "\n✓ All essential tables found!\n\n";
    }

    // Check sample data
    echo "Checking sample data...\n";
    
    $dataChecks = [
        'roles' => 'SELECT COUNT(*) as count FROM roles',
        'users' => 'SELECT COUNT(*) as count FROM users',
        'product_categories' => 'SELECT COUNT(*) as count FROM product_categories',
        'settings' => 'SELECT COUNT(*) as count FROM settings'
    ];

    foreach ($dataChecks as $table => $query) {
        if (in_array($table, $tables)) {
            try {
                $stmt = $pdo->query($query);
                $result = $stmt->fetch();
                $count = $result['count'];
                
                if ($count > 0) {
                    echo "✓ Table '$table' has $count records\n";
                } else {
                    echo "! Table '$table' is empty\n";
                }
            } catch (Exception $e) {
                echo "✗ Error checking table '$table': " . $e->getMessage() . "\n";
            }
        }
    }

    // Test Laravel connection
    echo "\nTesting Laravel database configuration...\n";
    
    // Simple Artisan command test
    $output = shell_exec('php artisan config:show database.default 2>&1');
    if (strpos($output, 'mysql') !== false) {
        echo "✓ Laravel is configured for MySQL\n";
    } else {
        echo "! Laravel database configuration may need adjustment\n";
    }

    // Check if manual database setup is enabled
    $manualSetup = $_ENV['MANUAL_DATABASE_SETUP'] ?? 'false';
    if (strtolower($manualSetup) === 'true') {
        echo "✓ Manual database setup is enabled\n";
    } else {
        echo "! Manual database setup is not enabled in .env\n";
        echo "  Set MANUAL_DATABASE_SETUP=true in your .env file\n";
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "VERIFICATION SUMMARY\n";
    echo str_repeat("=", 50) . "\n";
    
    if (empty($missingTables)) {
        echo "✅ Database setup is COMPLETE\n";
        echo "✅ All essential tables are present\n";
        echo "✅ Database connection is working\n";
        echo "\nYour Sellora application should be ready to use!\n";
    } else {
        echo "❌ Database setup is INCOMPLETE\n";
        echo "❌ Missing tables: " . implode(', ', $missingTables) . "\n";
        echo "\nPlease import the SQL files using:\n";
        echo "- ./import-database.sh (for automated import)\n";
        echo "- Or manually import database/sql/fresh_install.sql\n";
    }

    echo "\nFor production deployment:\n";
    echo "1. Import database/sql/fresh_install.sql for clean setup\n";
    echo "2. Import database/sql/complete_mysql.sql for development with sample data\n";
    echo "3. Set MANUAL_DATABASE_SETUP=true in .env\n";
    echo "4. Run: php artisan optimize for production caching\n";

} catch (PDOException $e) {
    echo "❌ Database connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    
    echo "Common solutions:\n";
    echo "1. Check database credentials in .env file\n";
    echo "2. Ensure MySQL service is running\n";
    echo "3. Verify database exists and user has permissions\n";
    echo "4. Check host and port settings\n";
    
} catch (Exception $e) {
    echo "❌ Verification failed!\n";
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n";
