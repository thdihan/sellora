<?php

/**
 * Advanced SQLite to MySQL Schema Converter
 * 
 * This script creates proper MySQL schema from Laravel migrations
 */

require_once 'vendor/autoload.php';

echo "Advanced MySQL Schema Generator\n";
echo "==============================\n\n";

try {
    // Connect to SQLite database
    $sqliteDb = 'database/database.sqlite';
    $pdo = new PDO("sqlite:$sqliteDb");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create SQL files directory
    $sqlDir = 'database/sql';
    if (!is_dir($sqlDir)) {
        mkdir($sqlDir, 0755, true);
    }

    // Get all tables with their info
    $tablesQuery = $pdo->query("
        SELECT name, sql 
        FROM sqlite_master 
        WHERE type='table' AND name NOT LIKE 'sqlite_%' 
        ORDER BY name
    ");
    $tables = $tablesQuery->fetchAll(PDO::FETCH_ASSOC);

    // Create optimized schema
    $schemaContent = generateMySQLSchema($pdo, $tables);
    
    // Create data export
    $dataContent = generateMySQLData($pdo, $tables);

    // Write files
    file_put_contents("$sqlDir/schema_mysql.sql", $schemaContent);
    file_put_contents("$sqlDir/data_mysql.sql", $dataContent);
    file_put_contents("$sqlDir/complete_mysql.sql", $schemaContent . "\n\n" . $dataContent);

    // Create a clean schema-only file for fresh installations
    $cleanSchema = generateCleanSchema($pdo, $tables);
    file_put_contents("$sqlDir/fresh_install.sql", $cleanSchema);

    echo "MySQL files generated successfully!\n\n";
    echo "Files created:\n";
    echo "- schema_mysql.sql (Structure with proper indexes)\n";
    echo "- data_mysql.sql (Sample data)\n";
    echo "- complete_mysql.sql (Structure + Data)\n";
    echo "- fresh_install.sql (Clean structure for production)\n\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

function generateMySQLSchema($pdo, $tables) {
    $content = "-- Sellora MySQL Database Schema\n";
    $content .= "-- Generated on " . date('Y-m-d H:i:s') . "\n";
    $content .= "-- Compatible with MySQL 5.7+ and MariaDB 10.3+\n\n";
    
    $content .= "SET FOREIGN_KEY_CHECKS = 0;\n";
    $content .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
    $content .= "SET AUTOCOMMIT = 0;\n";
    $content .= "START TRANSACTION;\n";
    $content .= "SET time_zone = '+00:00';\n\n";

    foreach ($tables as $table) {
        $tableName = $table['name'];
        echo "Generating schema for: $tableName\n";
        
        $content .= "-- Table: $tableName\n";
        $content .= "DROP TABLE IF EXISTS `$tableName`;\n";
        $content .= generateTableSchema($pdo, $tableName) . "\n\n";
    }

    // Add indexes and foreign keys
    $content .= generateIndexes($pdo, $tables);
    
    $content .= "COMMIT;\n";
    $content .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    return $content;
}

function generateTableSchema($pdo, $tableName) {
    // Get column information
    $columnsQuery = $pdo->query("PRAGMA table_info(`$tableName`)");
    $columns = $columnsQuery->fetchAll(PDO::FETCH_ASSOC);

    $sql = "CREATE TABLE `$tableName` (\n";
    $columnDefs = [];
    $primaryKeys = [];

    foreach ($columns as $col) {
        $colName = $col['name'];
        $colType = $col['type'];
        $notNull = $col['notnull'] ? 'NOT NULL' : 'NULL';
        $defaultValue = $col['dflt_value'];
        $isPk = $col['pk'];

        if ($isPk) {
            $primaryKeys[] = $colName;
        }

        // Convert SQLite types to MySQL
        $mysqlType = convertColumnType($colType, $colName);
        
        $columnDef = "  `$colName` $mysqlType";
        
        // Handle auto increment
        if ($isPk && ($colType === 'INTEGER' || stripos($mysqlType, 'INT') !== false)) {
            $columnDef .= ' AUTO_INCREMENT';
        }
        
        // Handle NOT NULL
        if ($col['notnull'] && $defaultValue === null && !$isPk) {
            $columnDef .= ' NOT NULL';
        }
        
        // Handle default values
        if ($defaultValue !== null && $defaultValue !== '') {
            if (is_numeric($defaultValue) || $defaultValue === 'CURRENT_TIMESTAMP') {
                $columnDef .= " DEFAULT $defaultValue";
            } else {
                $columnDef .= " DEFAULT '" . str_replace("'", "''", $defaultValue) . "'";
            }
        }

        $columnDefs[] = $columnDef;
    }

    $sql .= implode(",\n", $columnDefs);

    // Add primary key
    if (!empty($primaryKeys)) {
        $pkList = '`' . implode('`, `', $primaryKeys) . '`';
        $sql .= ",\n  PRIMARY KEY ($pkList)";
    }

    $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    return $sql;
}

function convertColumnType($sqliteType, $columnName) {
    $type = strtoupper($sqliteType);
    
    // Handle specific column patterns
    if (stripos($columnName, 'email') !== false) {
        return 'VARCHAR(191)';
    }
    if (stripos($columnName, 'password') !== false) {
        return 'VARCHAR(255)';
    }
    if (stripos($columnName, 'token') !== false) {
        return 'VARCHAR(255)';
    }
    if (stripos($columnName, 'remember_token') !== false) {
        return 'VARCHAR(100)';
    }
    if (in_array($columnName, ['created_at', 'updated_at', 'deleted_at', 'email_verified_at'])) {
        return 'TIMESTAMP NULL';
    }

    // Handle SQLite types
    switch ($type) {
        case 'INTEGER':
            if (stripos($columnName, '_id') !== false || $columnName === 'id') {
                return 'BIGINT UNSIGNED';
            }
            return 'INT';
            
        case 'TEXT':
            if (stripos($columnName, 'description') !== false || 
                stripos($columnName, 'content') !== false ||
                stripos($columnName, 'body') !== false) {
                return 'TEXT';
            }
            return 'VARCHAR(255)';
            
        case 'REAL':
        case 'NUMERIC':
            return 'DECIMAL(10,2)';
            
        case 'BLOB':
            return 'LONGBLOB';
            
        case 'BOOLEAN':
            return 'TINYINT(1)';
            
        default:
            // Handle VARCHAR with length
            if (preg_match('/VARCHAR\((\d+)\)/i', $type, $matches)) {
                $length = (int)$matches[1];
                return $length > 191 ? 'TEXT' : "VARCHAR($length)";
            }
            
            // Handle DECIMAL
            if (preg_match('/DECIMAL\((\d+),(\d+)\)/i', $type, $matches)) {
                return "DECIMAL({$matches[1]},{$matches[2]})";
            }
            
            return 'VARCHAR(255)';
    }
}

function generateIndexes($pdo, $tables) {
    $content = "-- Indexes and Foreign Keys\n";
    
    foreach ($tables as $table) {
        $tableName = $table['name'];
        
        // Get indexes
        $indexQuery = $pdo->query("PRAGMA index_list(`$tableName`)");
        $indexes = $indexQuery->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($indexes as $index) {
            if ($index['origin'] === 'c') { // Created index, not auto
                $indexName = $index['name'];
                $indexInfoQuery = $pdo->query("PRAGMA index_info(`$indexName`)");
                $indexInfo = $indexInfoQuery->fetchAll(PDO::FETCH_ASSOC);
                
                $columns = [];
                foreach ($indexInfo as $info) {
                    $columns[] = '`' . $info['name'] . '`';
                }
                
                if (!empty($columns)) {
                    $unique = $index['unique'] ? 'UNIQUE ' : '';
                    $content .= "CREATE {$unique}INDEX `$indexName` ON `$tableName` (" . implode(', ', $columns) . ");\n";
                }
            }
        }
    }
    
    return $content . "\n";
}

function generateMySQLData($pdo, $tables) {
    $content = "-- Sellora MySQL Data Export\n";
    $content .= "-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
    
    $content .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    foreach ($tables as $table) {
        $tableName = $table['name'];
        
        // Skip certain tables for clean exports
        if (in_array($tableName, ['cache', 'cache_locks', 'sessions', 'jobs', 'failed_jobs', 'job_batches'])) {
            continue;
        }
        
        echo "Exporting data for: $tableName\n";
        
        $dataQuery = $pdo->query("SELECT * FROM `$tableName`");
        $rows = $dataQuery->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $content .= "-- Data for table `$tableName`\n";
            $content .= "TRUNCATE TABLE `$tableName`;\n";
            
            $columns = array_keys($rows[0]);
            $columnsList = '`' . implode('`, `', $columns) . '`';
            
            // Insert in batches
            $batchSize = 100;
            $batches = array_chunk($rows, $batchSize);
            
            foreach ($batches as $batch) {
                $content .= "INSERT INTO `$tableName` ($columnsList) VALUES\n";
                
                $values = [];
                foreach ($batch as $row) {
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
                
                $content .= implode(",\n", $values) . ";\n\n";
            }
        }
    }

    $content .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    return $content;
}

function generateCleanSchema($pdo, $tables) {
    $content = "-- Sellora Clean MySQL Schema (Production Ready)\n";
    $content .= "-- Generated on " . date('Y-m-d H:i:s') . "\n";
    $content .= "-- This file contains only the structure, no sample data\n\n";
    
    $content .= "SET FOREIGN_KEY_CHECKS = 0;\n";
    $content .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
    $content .= "SET time_zone = '+00:00';\n\n";

    foreach ($tables as $table) {
        $tableName = $table['name'];
        
        // Skip cache and temporary tables in clean schema
        if (in_array($tableName, ['cache', 'cache_locks', 'sessions', 'jobs', 'failed_jobs', 'job_batches'])) {
            continue;
        }
        
        $content .= "-- Table: $tableName\n";
        $content .= "DROP TABLE IF EXISTS `$tableName`;\n";
        $content .= generateTableSchema($pdo, $tableName) . "\n\n";
    }

    // Add essential data only (roles, settings, etc.)
    $content .= "-- Essential data\n";
    $content .= generateEssentialData($pdo);

    $content .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    return $content;
}

function generateEssentialData($pdo) {
    $content = "\n-- Roles (Essential)\n";
    $rolesQuery = $pdo->query("SELECT * FROM roles");
    $roles = $rolesQuery->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($roles)) {
        $content .= "INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES\n";
        $values = [];
        foreach ($roles as $role) {
            $values[] = sprintf("(%d, '%s', '%s', '%s', '%s', '%s')",
                $role['id'],
                addslashes($role['name']),
                addslashes($role['display_name']),
                addslashes($role['description'] ?? ''),
                $role['created_at'],
                $role['updated_at']
            );
        }
        $content .= implode(",\n", $values) . ";\n\n";
    }

    return $content;
}

echo "Advanced MySQL schema generation completed.\n";
