<?php

/**
 * Database Schema and Data Exporter for MySQL
 * 
 * This script exports the current SQLite database to MySQL-compatible SQL files
 * Run this after setting up your database with migrations and seeders
 */

require_once 'vendor/autoload.php';

// Load environment variables
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

echo "Sellora Database Schema & Data Exporter\n";
echo "=====================================\n\n";

try {
    // Connect to SQLite database
    $sqliteDb = $_ENV['DB_DATABASE'] ?? 'database/database.sqlite';
    $pdo = new PDO("sqlite:$sqliteDb");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create SQL files directory
    $sqlDir = 'database/sql';
    if (!is_dir($sqlDir)) {
        mkdir($sqlDir, 0755, true);
    }

    echo "Exporting database schema and data...\n\n";

    // Get all tables
    $tablesQuery = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_COLUMN);

    // Create schema file
    $schemaFile = "$sqlDir/01_schema.sql";
    $schemaContent = "-- Sellora Database Schema for MySQL\n";
    $schemaContent .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
    $schemaContent .= "SET FOREIGN_KEY_CHECKS = 0;\n";
    $schemaContent .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
    $schemaContent .= "SET time_zone = '+00:00';\n\n";

    // Create data file
    $dataFile = "$sqlDir/02_data.sql";
    $dataContent = "-- Sellora Database Data for MySQL\n";
    $dataContent .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
    $dataContent .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    foreach ($tables as $table) {
        echo "Processing table: $table\n";

        // Get table schema
        $schemaQuery = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='$table'");
        $schema = $schemaQuery->fetchColumn();

        // Convert SQLite schema to MySQL
        $mysqlSchema = convertSQLiteToMySQL($schema, $table);
        $schemaContent .= $mysqlSchema . "\n\n";

        // Get table data
        $dataQuery = $pdo->query("SELECT * FROM `$table`");
        $rows = $dataQuery->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $dataContent .= "-- Data for table `$table`\n";
            
            // Get column names
            $columns = array_keys($rows[0]);
            $columnsList = '`' . implode('`, `', $columns) . '`';
            
            $dataContent .= "INSERT INTO `$table` ($columnsList) VALUES\n";
            
            $values = [];
            foreach ($rows as $row) {
                $rowValues = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $rowValues[] = 'NULL';
                    } elseif (is_numeric($value)) {
                        $rowValues[] = $value;
                    } else {
                        $rowValues[] = "'" . addslashes($value) . "'";
                    }
                }
                $values[] = '(' . implode(', ', $rowValues) . ')';
            }
            
            $dataContent .= implode(",\n", $values) . ";\n\n";
        }
    }

    $schemaContent .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    $dataContent .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    // Write files
    file_put_contents($schemaFile, $schemaContent);
    file_put_contents($dataFile, $dataContent);

    // Create combined file
    $combinedFile = "$sqlDir/sellora_complete.sql";
    $combinedContent = $schemaContent . "\n" . $dataContent;
    file_put_contents($combinedFile, $combinedContent);

    echo "\nExport completed successfully!\n\n";
    echo "Files created:\n";
    echo "- $schemaFile (Database structure only)\n";
    echo "- $dataFile (Data only)\n";
    echo "- $combinedFile (Complete database)\n\n";

    echo "To import into MySQL:\n";
    echo "1. Create your MySQL database\n";
    echo "2. Import using: mysql -u username -p database_name < database/sql/sellora_complete.sql\n";
    echo "3. Or import via phpMyAdmin\n\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

function convertSQLiteToMySQL($schema, $tableName) {
    // Remove CREATE TABLE and get column definitions
    $schema = preg_replace('/CREATE TABLE ["`]?' . $tableName . '["`]?\s*\(/i', '', $schema);
    $schema = rtrim($schema, ');');
    
    // Start MySQL CREATE TABLE
    $mysql = "CREATE TABLE `$tableName` (\n";
    
    // Split into lines and process each column
    $lines = explode(',', $schema);
    $columns = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        // Skip constraints for now, we'll add them back
        if (stripos($line, 'CONSTRAINT') !== false || 
            stripos($line, 'FOREIGN KEY') !== false ||
            stripos($line, 'UNIQUE') !== false) {
            continue;
        }
        
        // Convert column definition
        $mysqlLine = convertColumnDefinition($line);
        if ($mysqlLine) {
            $columns[] = '  ' . $mysqlLine;
        }
    }
    
    $mysql .= implode(",\n", $columns);
    
    // Add primary key if not already defined
    if (!stripos($schema, 'PRIMARY KEY')) {
        if (stripos($schema, '`id`') !== false || stripos($schema, 'id ') !== false) {
            $mysql .= ",\n  PRIMARY KEY (`id`)";
        }
    }
    
    $mysql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    return $mysql;
}

function convertColumnDefinition($line) {
    $line = trim($line);
    
    // Handle different column types
    $line = preg_replace('/\bINTEGER\b/i', 'INT', $line);
    $line = preg_replace('/\bTEXT\b/i', 'TEXT', $line);
    $line = preg_replace('/\bREAL\b/i', 'DOUBLE', $line);
    $line = preg_replace('/\bBLOB\b/i', 'LONGBLOB', $line);
    
    // Handle AUTOINCREMENT
    $line = preg_replace('/\bAUTOINCREMENT\b/i', 'AUTO_INCREMENT', $line);
    
    // Handle timestamps
    $line = preg_replace('/\bDATETIME\b/i', 'TIMESTAMP', $line);
    
    // Handle varchar without length
    if (preg_match('/VARCHAR(?!\()/i', $line)) {
        $line = preg_replace('/\bVARCHAR\b/i', 'VARCHAR(255)', $line);
    }
    
    // Add backticks around column names
    if (preg_match('/^(\w+)\s+(.+)$/', $line, $matches)) {
        $columnName = $matches[1];
        $columnDef = $matches[2];
        return "`$columnName` $columnDef";
    }
    
    return $line;
}

echo "Database export script completed.\n";
