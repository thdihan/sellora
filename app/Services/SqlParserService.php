<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SqlParserService
{
    protected array $allowedStatements = [
        'INSERT',
        'UPDATE',
        'SELECT'
    ];

    protected array $dangerousKeywords = [
        'DROP',
        'DELETE',
        'TRUNCATE',
        'ALTER',
        'CREATE',
        'GRANT',
        'REVOKE',
        'EXEC',
        'EXECUTE',
        'CALL',
        'LOAD_FILE',
        'INTO OUTFILE',
        'INTO DUMPFILE'
    ];

    public function parseSqlFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new Exception('SQL file not found: ' . $filePath);
        }

        $content = file_get_contents($filePath);
        return $this->parseSqlContent($content);
    }

    public function parseSqlContent(string $content): array
    {
        // Remove comments
        $content = $this->removeComments($content);
        
        // Split into statements
        $statements = $this->splitStatements($content);
        
        // Validate and filter statements
        $validStatements = [];
        $errors = [];
        
        foreach ($statements as $index => $statement) {
            try {
                $this->validateStatement($statement);
                $validStatements[] = [
                    'statement' => $statement,
                    'type' => $this->getStatementType($statement),
                    'line' => $index + 1
                ];
            } catch (Exception $e) {
                $errors[] = [
                    'line' => $index + 1,
                    'statement' => substr($statement, 0, 100) . '...',
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return [
            'statements' => $validStatements,
            'errors' => $errors,
            'total_statements' => count($statements),
            'valid_statements' => count($validStatements),
            'error_count' => count($errors)
        ];
    }

    protected function removeComments(string $content): string
    {
        // Remove single-line comments (-- and #)
        $content = preg_replace('/--.*$/m', '', $content);
        $content = preg_replace('/#.*$/m', '', $content);
        
        // Remove multi-line comments (/* */)
        $content = preg_replace('/\/\*.*?\*\//s', '', $content);
        
        return $content;
    }

    protected function splitStatements(string $content): array
    {
        // Split by semicolons, but be careful about semicolons in strings
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = null;
        $escaped = false;
        
        for ($i = 0; $i < strlen($content); $i++) {
            $char = $content[$i];
            
            if ($escaped) {
                $current .= $char;
                $escaped = false;
                continue;
            }
            
            if ($char === '\\') {
                $escaped = true;
                $current .= $char;
                continue;
            }
            
            if (!$inString && ($char === '"' || $char === "'")) {
                $inString = true;
                $stringChar = $char;
                $current .= $char;
            } elseif ($inString && $char === $stringChar) {
                $inString = false;
                $stringChar = null;
                $current .= $char;
            } elseif (!$inString && $char === ';') {
                $statement = trim($current);
                if (!empty($statement)) {
                    $statements[] = $statement;
                }
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        // Add the last statement if it doesn't end with semicolon
        $statement = trim($current);
        if (!empty($statement)) {
            $statements[] = $statement;
        }
        
        return array_filter($statements, fn($stmt) => !empty(trim($stmt)));
    }

    protected function validateStatement(string $statement): void
    {
        $statement = trim($statement);
        
        if (empty($statement)) {
            throw new Exception('Empty statement');
        }
        
        // Check for dangerous keywords
        $upperStatement = strtoupper($statement);
        foreach ($this->dangerousKeywords as $keyword) {
            if (strpos($upperStatement, $keyword) !== false) {
                throw new Exception("Dangerous keyword detected: {$keyword}");
            }
        }
        
        // Check if statement type is allowed
        $type = $this->getStatementType($statement);
        if (!in_array($type, $this->allowedStatements)) {
            throw new Exception("Statement type not allowed: {$type}");
        }
        
        // Additional validation for INSERT statements
        if ($type === 'INSERT') {
            $this->validateInsertStatement($statement);
        }
    }

    protected function validateInsertStatement(string $statement): void
    {
        // Check for basic INSERT syntax
        if (!preg_match('/^INSERT\s+INTO\s+\w+/i', $statement)) {
            throw new Exception('Invalid INSERT statement syntax');
        }
        
        // Check for potential SQL injection patterns
        $dangerousPatterns = [
            '/UNION\s+SELECT/i',
            '/;\s*(DROP|DELETE|TRUNCATE|ALTER)/i',
            '/\bEXEC\b/i',
            '/\bEXECUTE\b/i',
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $statement)) {
                throw new Exception('Potentially dangerous SQL pattern detected');
            }
        }
    }

    protected function getStatementType(string $statement): string
    {
        $statement = trim(strtoupper($statement));
        
        if (strpos($statement, 'INSERT') === 0) {
            return 'INSERT';
        } elseif (strpos($statement, 'UPDATE') === 0) {
            return 'UPDATE';
        } elseif (strpos($statement, 'SELECT') === 0) {
            return 'SELECT';
        }
        
        // Extract the first word
        $words = explode(' ', $statement);
        return $words[0] ?? 'UNKNOWN';
    }

    public function executeStatements(array $statements): array
    {
        $results = [];
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($statements as $statementData) {
            $statement = $statementData['statement'];
            $line = $statementData['line'];
            
            try {
                DB::beginTransaction();
                
                // Execute the statement
                $result = DB::statement($statement);
                
                DB::commit();
                
                $results[] = [
                    'line' => $line,
                    'status' => 'success',
                    'statement' => substr($statement, 0, 100) . '...',
                    'affected_rows' => DB::affectingStatement($statement) ?? 0
                ];
                
                $successCount++;
                
            } catch (Exception $e) {
                DB::rollBack();
                
                $results[] = [
                    'line' => $line,
                    'status' => 'error',
                    'statement' => substr($statement, 0, 100) . '...',
                    'error' => $e->getMessage()
                ];
                
                $errorCount++;
                
                Log::warning('SQL statement execution failed', [
                    'line' => $line,
                    'statement' => $statement,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return [
            'results' => $results,
            'summary' => [
                'total' => count($statements),
                'success' => $successCount,
                'errors' => $errorCount
            ]
        ];
    }

    public function validateSqlFile(string $filePath): array
    {
        try {
            $parseResult = $this->parseSqlFile($filePath);
            
            return [
                'valid' => $parseResult['error_count'] === 0,
                'total_statements' => $parseResult['total_statements'],
                'valid_statements' => $parseResult['valid_statements'],
                'errors' => $parseResult['errors'],
                'warnings' => $this->generateWarnings($parseResult['statements'])
            ];
            
        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    protected function generateWarnings(array $statements): array
    {
        $warnings = [];
        
        foreach ($statements as $statement) {
            $sql = strtoupper($statement['statement']);
            
            // Warn about UPDATE without WHERE
            if (strpos($sql, 'UPDATE') === 0 && strpos($sql, 'WHERE') === false) {
                $warnings[] = [
                    'line' => $statement['line'],
                    'type' => 'UPDATE_WITHOUT_WHERE',
                    'message' => 'UPDATE statement without WHERE clause may affect all rows'
                ];
            }
            
            // Warn about large batch inserts
            if (strpos($sql, 'INSERT') === 0 && substr_count($sql, 'VALUES') > 100) {
                $warnings[] = [
                    'line' => $statement['line'],
                    'type' => 'LARGE_BATCH_INSERT',
                    'message' => 'Large batch insert may cause performance issues'
                ];
            }
        }
        
        return $warnings;
    }
}