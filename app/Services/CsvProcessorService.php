<?php

namespace App\Services;

use League\Csv\Reader;
use League\Csv\Writer;
use League\Csv\Exception as CsvException;
use Exception;
use Illuminate\Support\Facades\Log;

class CsvProcessorService
{
    protected array $supportedEncodings = [
        'UTF-8',
        'ISO-8859-1',
        'Windows-1252'
    ];

    protected array $supportedDelimiters = [
        ',',
        ';',
        '\t',
        '|'
    ];

    public function validateCsvFile(string $filePath): array
    {
        try {
            if (!file_exists($filePath)) {
                throw new Exception('CSV file not found: ' . $filePath);
            }

            $fileSize = filesize($filePath);
            if ($fileSize === 0) {
                throw new Exception('CSV file is empty');
            }

            // Detect encoding
            $encoding = $this->detectEncoding($filePath);
            
            // Detect delimiter
            $delimiter = $this->detectDelimiter($filePath);
            
            // Create CSV reader
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setDelimiter($delimiter);
            
            // Try to detect header
            $csv->setHeaderOffset(0);
            $headers = $csv->getHeader();
            
            // Count records
            $recordCount = iterator_count($csv->getRecords());
            
            // Validate structure
            $validation = $this->validateCsvStructure($csv);
            
            return [
                'valid' => $validation['valid'],
                'file_info' => [
                    'size' => $fileSize,
                    'encoding' => $encoding,
                    'delimiter' => $delimiter,
                    'headers' => $headers,
                    'record_count' => $recordCount
                ],
                'validation' => $validation,
                'errors' => $validation['errors'] ?? [],
                'warnings' => $validation['warnings'] ?? []
            ];
            
        } catch (Exception $e) {
            return [
                'valid' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function processCsvFile(string $filePath, array $options = []): array
    {
        try {
            $csv = Reader::createFromPath($filePath, 'r');
            
            // Set delimiter if provided
            if (isset($options['delimiter'])) {
                $csv->setDelimiter($options['delimiter']);
            } else {
                $csv->setDelimiter($this->detectDelimiter($filePath));
            }
            
            // Set header offset
            $headerOffset = $options['header_offset'] ?? 0;
            $csv->setHeaderOffset($headerOffset);
            
            // Get records
            $records = iterator_to_array($csv->getRecords());
            
            // Apply field mappings if provided
            if (isset($options['field_mappings'])) {
                $records = $this->applyFieldMappings($records, $options['field_mappings']);
            }
            
            // Validate records if validation rules provided
            $validationResults = [];
            if (isset($options['validation_rules'])) {
                $validationResults = $this->validateRecords($records, $options['validation_rules']);
            }
            
            return [
                'success' => true,
                'headers' => $csv->getHeader(),
                'records' => $records,
                'record_count' => count($records),
                'validation' => $validationResults
            ];
            
        } catch (Exception $e) {
            Log::error('CSV processing failed', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function exportToCsv(array $data, string $filePath, array $options = []): bool
    {
        try {
            $csv = Writer::createFromPath($filePath, 'w+');
            
            // Set delimiter
            $delimiter = $options['delimiter'] ?? ',';
            $csv->setDelimiter($delimiter);
            
            // Add BOM for UTF-8 if requested
            if ($options['add_bom'] ?? false) {
                $csv->setOutputBOM(Writer::BOM_UTF8);
            }
            
            // Insert headers if data is not empty
            if (!empty($data)) {
                $headers = array_keys($data[0]);
                $csv->insertOne($headers);
                
                // Insert data rows
                foreach ($data as $record) {
                    $csv->insertOne(array_values($record));
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            Log::error('CSV export failed', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    protected function detectEncoding(string $filePath): string
    {
        $content = file_get_contents($filePath, false, null, 0, 1024); // Read first 1KB
        
        foreach ($this->supportedEncodings as $encoding) {
            if (mb_check_encoding($content, $encoding)) {
                return $encoding;
            }
        }
        
        return 'UTF-8'; // Default fallback
    }

    protected function detectDelimiter(string $filePath): string
    {
        $content = file_get_contents($filePath, false, null, 0, 1024); // Read first 1KB
        $lines = explode("\n", $content);
        
        if (empty($lines)) {
            return ','; // Default fallback
        }
        
        $delimiterCounts = [];
        
        foreach ($this->supportedDelimiters as $delimiter) {
            $count = 0;
            foreach (array_slice($lines, 0, 5) as $line) { // Check first 5 lines
                $count += substr_count($line, $delimiter);
            }
            $delimiterCounts[$delimiter] = $count;
        }
        
        // Return delimiter with highest count
        arsort($delimiterCounts);
        return array_key_first($delimiterCounts) ?: ',';
    }

    protected function validateCsvStructure($csv): array
    {
        $errors = [];
        $warnings = [];
        
        try {
            $headers = $csv->getHeader();
            $records = iterator_to_array($csv->getRecords());
            
            // Check for empty headers
            foreach ($headers as $index => $header) {
                if (empty(trim($header))) {
                    $warnings[] = "Empty header found at column " . ($index + 1);
                }
            }
            
            // Check for duplicate headers
            $duplicateHeaders = array_diff_assoc($headers, array_unique($headers));
            if (!empty($duplicateHeaders)) {
                $warnings[] = "Duplicate headers found: " . implode(', ', $duplicateHeaders);
            }
            
            // Check record consistency
            $expectedColumnCount = count($headers);
            $inconsistentRows = [];
            
            foreach ($records as $offset => $record) {
                $columnCount = count($record);
                if ($columnCount !== $expectedColumnCount) {
                    $inconsistentRows[] = $offset + 2; // +2 for header and 0-based index
                }
            }
            
            if (!empty($inconsistentRows)) {
                $errors[] = "Inconsistent column count in rows: " . implode(', ', array_slice($inconsistentRows, 0, 10));
                if (count($inconsistentRows) > 10) {
                    $errors[] = "... and " . (count($inconsistentRows) - 10) . " more rows";
                }
            }
            
            // Check for completely empty rows
            $emptyRows = [];
            foreach ($records as $offset => $record) {
                if (empty(array_filter($record, fn($value) => !empty(trim($value))))) {
                    $emptyRows[] = $offset + 2;
                }
            }
            
            if (!empty($emptyRows)) {
                $warnings[] = "Empty rows found: " . implode(', ', array_slice($emptyRows, 0, 5));
                if (count($emptyRows) > 5) {
                    $warnings[] = "... and " . (count($emptyRows) - 5) . " more empty rows";
                }
            }
            
        } catch (Exception $e) {
            $errors[] = "Structure validation failed: " . $e->getMessage();
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    protected function applyFieldMappings(array $records, array $mappings): array
    {
        $mappedRecords = [];
        
        foreach ($records as $record) {
            $mappedRecord = [];
            foreach ($mappings as $sourceField => $targetField) {
                if (isset($record[$sourceField])) {
                    $mappedRecord[$targetField] = $record[$sourceField];
                }
            }
            $mappedRecords[] = $mappedRecord;
        }
        
        return $mappedRecords;
    }

    protected function validateRecords(array $records, array $rules): array
    {
        $validRecords = [];
        $invalidRecords = [];
        
        foreach ($records as $index => $record) {
            $errors = [];
            
            foreach ($rules as $field => $fieldRules) {
                $value = $record[$field] ?? null;
                
                foreach ($fieldRules as $rule) {
                    switch ($rule) {
                        case 'required':
                            if (empty($value)) {
                                $errors[] = "Field '{$field}' is required";
                            }
                            break;
                            
                        case 'email':
                            if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $errors[] = "Field '{$field}' must be a valid email";
                            }
                            break;
                            
                        case 'numeric':
                            if (!empty($value) && !is_numeric($value)) {
                                $errors[] = "Field '{$field}' must be numeric";
                            }
                            break;
                            
                        case 'date':
                            if (!empty($value) && !strtotime($value)) {
                                $errors[] = "Field '{$field}' must be a valid date";
                            }
                            break;
                    }
                }
            }
            
            if (empty($errors)) {
                $validRecords[] = $record;
            } else {
                $invalidRecords[] = [
                    'row' => $index + 2, // +2 for header and 0-based index
                    'record' => $record,
                    'errors' => $errors
                ];
            }
        }
        
        return [
            'valid_count' => count($validRecords),
            'invalid_count' => count($invalidRecords),
            'valid_records' => $validRecords,
            'invalid_records' => $invalidRecords
        ];
    }

    public function generateSampleCsv(string $module): string
    {
        $samples = [
            'users' => [
                ['name', 'email', 'password', 'role'],
                ['John Doe', 'john@example.com', 'password123', 'user'],
                ['Jane Smith', 'jane@example.com', 'password456', 'admin']
            ],
            'products' => [
                ['name', 'description', 'price', 'category'],
                ['Product 1', 'Sample product description', '29.99', 'Electronics'],
                ['Product 2', 'Another sample product', '49.99', 'Clothing']
            ],
            'orders' => [
                ['customer_email', 'total', 'status', 'order_date'],
                ['customer@example.com', '99.99', 'completed', '2024-01-15'],
                ['another@example.com', '149.99', 'pending', '2024-01-16']
            ]
        ];
        
        $data = $samples[$module] ?? $samples['users'];
        
        $csv = Writer::createFromString('');
        foreach ($data as $row) {
            $csv->insertOne($row);
        }
        
        return $csv->toString();
    }
}