<?php
/**
 * SQL Syntax Validator for MySQL Files
 * This script validates the syntax of our generated SQL files
 */

// Database connection parameters (use test database)
$host = 'localhost';
$username = 'root';
$password = '';
$testDbName = 'sellora_syntax_test';

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create test database
    echo "Creating test database...\n";
    $pdo->exec("DROP DATABASE IF EXISTS $testDbName");
    $pdo->exec("CREATE DATABASE $testDbName");
    $pdo->exec("USE $testDbName");
    
    // Test fresh_install.sql
    echo "\n=== Testing fresh_install.sql ===\n";
    $sql = file_get_contents(__DIR__ . '/database/sql/fresh_install.sql');
    
    // Remove comments and split into statements
    $statements = array_filter(
        array_map('trim', explode(';', preg_replace('/--.*$/m', '', $sql))),
        'strlen'
    );
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $index => $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            $pdo->exec($statement);
            $successCount++;
            echo ".";
        } catch (PDOException $e) {
            $errorCount++;
            echo "\nERROR in statement " . ($index + 1) . ":\n";
            echo "Statement: " . substr($statement, 0, 100) . "...\n";
            echo "Error: " . $e->getMessage() . "\n\n";
        }
    }
    
    echo "\n\nResults:\n";
    echo "✓ Successful statements: $successCount\n";
    echo "✗ Failed statements: $errorCount\n";
    
    if ($errorCount === 0) {
        echo "\n🎉 All SQL statements are valid!\n";
    } else {
        echo "\n⚠️  Some SQL statements have errors that need to be fixed.\n";
    }
    
    // Clean up
    $pdo->exec("DROP DATABASE $testDbName");
    
} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
    echo "Please make sure MySQL is running and accessible.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
