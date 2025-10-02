<?php

/**
 * MySQL Database Setup Script for Sellora
 * 
 * This script helps create the necessary database structure for MySQL
 * Run this after setting up your MySQL database in cPanel
 */

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "MySQL Database Setup for Sellora\n";
echo "================================\n\n";

try {
    // Database connection details
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? 3306;
    $database = $_ENV['DB_DATABASE'] ?? 'sellora_db';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';

    echo "Connecting to MySQL database...\n";
    echo "Host: $host\n";
    echo "Database: $database\n";
    echo "Username: $username\n\n";

    // Create PDO connection
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);

    // Create database if it doesn't exist
    echo "Creating database if it doesn't exist...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Switch to the database
    $pdo->exec("USE `$database`");

    echo "Database setup completed successfully!\n\n";
    
    echo "Next steps:\n";
    echo "1. Run: php artisan migrate --force\n";
    echo "2. Run: php artisan db:seed --force\n";
    echo "3. Run: php artisan storage:link\n";
    echo "4. Run: php artisan config:cache\n\n";

} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "Please check your database credentials in the .env file.\n";
    exit(1);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Setup completed successfully!\n";
