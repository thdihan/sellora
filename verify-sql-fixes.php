<?php
/**
 * Quick SQL Syntax Test
 * Tests a small portion of the SQL to verify fixes
 */

echo "=== SQL Syntax Verification ===\n\n";

// Test 1: Check for invalid default syntax
$sqlFile = __DIR__ . '/database/sql/fresh_install.sql';
$content = file_get_contents($sqlFile);

// Check for triple quotes (should be none)
$tripleQuotes = preg_match_all("/DEFAULT '''.+?'''/", $content);
echo "1. Triple quote defaults found: " . $tripleQuotes . " (should be 0) ";
echo $tripleQuotes === 0 ? "✅\n" : "❌\n";

// Check for display_name column (should be none)
$displayName = preg_match_all("/display_name/", $content);
echo "2. Display_name references found: " . $displayName . " (should be 0) ";
echo $displayName === 0 ? "✅\n" : "❌\n";

// Check for proper numeric defaults
$numericDefaults = preg_match_all("/DECIMAL\([0-9,]+\) DEFAULT [0-9.]+[^']$/m", $content);
echo "3. Proper numeric defaults found: " . $numericDefaults . " (should be > 0) ";
echo $numericDefaults > 0 ? "✅\n" : "❌\n";

// Check for roles table structure
$rolesTableMatch = preg_match("/CREATE TABLE `roles` \(\s*`id` BIGINT UNSIGNED AUTO_INCREMENT,\s*`name` VARCHAR\(255\) NOT NULL,\s*`description` TEXT,/", $content);
echo "4. Roles table structure correct: ";
echo $rolesTableMatch ? "✅\n" : "❌\n";

// Check for proper INSERT statement
$insertMatch = preg_match("/INSERT INTO `roles` \(`id`, `name`, `description`, `created_at`, `updated_at`\) VALUES/", $content);
echo "5. Roles INSERT statement correct: ";
echo $insertMatch ? "✅\n" : "❌\n";

echo "\n=== Summary ===\n";
$allPassed = ($tripleQuotes === 0) && ($displayName === 0) && ($numericDefaults > 0) && $rolesTableMatch && $insertMatch;
echo $allPassed ? "🎉 All checks passed! SQL file is ready for import.\n" : "⚠️ Some issues found. Check the details above.\n";

echo "\nFile size: " . number_format(filesize($sqlFile)) . " bytes\n";
echo "Last modified: " . date('Y-m-d H:i:s', filemtime($sqlFile)) . "\n";
?>
