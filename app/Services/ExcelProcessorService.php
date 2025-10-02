<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use Exception;
use Illuminate\Support\Facades\Log;

class ExcelProcessorService
{
    protected array $supportedFormats = [
        'xlsx',
        'xls',
        'csv',
        'ods'
    ];

    public function validateExcelFile(string $filePath): array
    {
        try {
            if (!file_exists($filePath)) {
                throw new Exception('Excel file not found: ' . $filePath);
            }

            $fileSize = filesize($filePath);
            if ($fileSize === 0) {
                throw new Exception('Excel file is empty');
            }

            // Detect file type
            $fileType = IOFactory::identify($filePath);
            
            // Load spreadsheet
            $reader = IOFactory::createReader($fileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            
            // Get worksheet info
            $worksheetInfo = [];
            foreach ($spreadsheet->getAllSheets() as $index => $worksheet) {
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                
                $worksheetInfo[] = [
                    'index' => $index,
                    'name' => $worksheet->getTitle(),
                    'rows' => $highestRow,
                    'columns' => $highestColumnIndex,
                    'highest_column' => $highestColumn
                ];
            }
            
            // Validate structure
            $validation = $this->validateExcelStructure($spreadsheet);
            
            return [
                'valid' => $validation['valid'],
                'file_info' => [
                    'size' => $fileSize,
                    'type' => $fileType,
                    'worksheet_count' => count($worksheetInfo),
                    'worksheets' => $worksheetInfo
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

    public function processExcelFile(string $filePath, array $options = []): array
    {
        try {
            $fileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($fileType);
            $reader->setReadDataOnly(true);
            
            // Set worksheet index if specified
            if (isset($options['worksheet_index'])) {
                $reader->setLoadSheetsOnly([$options['worksheet_index']]);
            }
            
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Get data range
            $startRow = $options['start_row'] ?? 1;
            $endRow = $options['end_row'] ?? $worksheet->getHighestRow();
            $startColumn = $options['start_column'] ?? 'A';
            $endColumn = $options['end_column'] ?? $worksheet->getHighestColumn();
            
            // Convert to array
            $data = $worksheet->rangeToArray(
                $startColumn . $startRow . ':' . $endColumn . $endRow,
                null,
                true,
                true,
                true
            );
            
            // Process headers and records
            $headers = [];
            $records = [];
            
            if (!empty($data)) {
                $headerRow = $options['header_row'] ?? $startRow;
                if (isset($data[$headerRow])) {
                    $headers = array_values($data[$headerRow]);
                    unset($data[$headerRow]);
                }
                
                // Convert remaining data to associative arrays
                foreach ($data as $rowIndex => $row) {
                    if (empty(array_filter($row))) {
                        continue; // Skip empty rows
                    }
                    
                    $record = [];
                    foreach ($row as $colIndex => $value) {
                        $headerIndex = array_search($colIndex, array_keys($row));
                        $header = $headers[$headerIndex] ?? 'Column_' . $colIndex;
                        $record[$header] = $value;
                    }
                    $records[] = $record;
                }
            }
            
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
                'headers' => $headers,
                'records' => $records,
                'record_count' => count($records),
                'validation' => $validationResults
            ];
            
        } catch (Exception $e) {
            Log::error('Excel processing failed', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function exportToExcel(array $data, string $filePath, array $options = []): bool
    {
        try {
            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Set worksheet name if provided
            if (isset($options['worksheet_name'])) {
                $worksheet->setTitle($options['worksheet_name']);
            }
            
            if (!empty($data)) {
                // Add headers
                $headers = array_keys($data[0]);
                $worksheet->fromArray($headers, null, 'A1');
                
                // Style headers if requested
                if ($options['style_headers'] ?? true) {
                    $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
                    $worksheet->getStyle($headerRange)->getFont()->setBold(true);
                    $worksheet->getStyle($headerRange)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('E0E0E0');
                }
                
                // Add data rows
                $rowIndex = 2;
                foreach ($data as $record) {
                    $worksheet->fromArray(array_values($record), null, 'A' . $rowIndex);
                    $rowIndex++;
                }
                
                // Auto-size columns if requested
                if ($options['auto_size'] ?? true) {
                    foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $column) {
                        $worksheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
            
            // Determine writer type from file extension
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            switch ($extension) {
                case 'csv':
                    $writer = new Csv($spreadsheet);
                    break;
                case 'xlsx':
                default:
                    $writer = new Xlsx($spreadsheet);
                    break;
            }
            
            $writer->save($filePath);
            
            return true;
            
        } catch (Exception $e) {
            Log::error('Excel export failed', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    protected function validateExcelStructure(Spreadsheet $spreadsheet): array
    {
        $errors = [];
        $warnings = [];
        
        try {
            $worksheetCount = $spreadsheet->getSheetCount();
            
            if ($worksheetCount === 0) {
                $errors[] = 'No worksheets found in the Excel file';
                return [
                    'valid' => false,
                    'errors' => $errors,
                    'warnings' => $warnings
                ];
            }
            
            foreach ($spreadsheet->getAllSheets() as $index => $worksheet) {
                $worksheetName = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                
                // Check if worksheet is empty
                if ($highestRow <= 1) {
                    $warnings[] = "Worksheet '{$worksheetName}' appears to be empty or has only headers";
                    continue;
                }
                
                // Check for merged cells
                $mergedCells = $worksheet->getMergeCells();
                if (!empty($mergedCells)) {
                    $warnings[] = "Worksheet '{$worksheetName}' contains merged cells which may affect data processing";
                }
                
                // Check first row for potential headers
                $firstRow = $worksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, false, false)[0];
                $emptyHeaders = 0;
                foreach ($firstRow as $cell) {
                    if (empty(trim($cell))) {
                        $emptyHeaders++;
                    }
                }
                
                if ($emptyHeaders > 0) {
                    $warnings[] = "Worksheet '{$worksheetName}' has {$emptyHeaders} empty header cells";
                }
            }
            
        } catch (Exception $e) {
            $errors[] = 'Structure validation failed: ' . $e->getMessage();
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
                    'row' => $index + 2,
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

    public function generateSampleExcel(string $module): string
    {
        $samples = [
            'users' => [
                ['name', 'email', 'password', 'role'],
                ['John Doe', 'john@example.com', 'password123', 'user'],
                ['Jane Smith', 'jane@example.com', 'password456', 'admin']
            ],
            'products' => [
                ['name', 'description', 'price', 'category'],
                ['Product 1', 'Sample product description', 29.99, 'Electronics'],
                ['Product 2', 'Another sample product', 49.99, 'Clothing']
            ],
            'orders' => [
                ['customer_email', 'total', 'status', 'order_date'],
                ['customer@example.com', 99.99, 'completed', '2024-01-15'],
                ['another@example.com', 149.99, 'pending', '2024-01-16']
            ]
        ];
        
        $data = $samples[$module] ?? $samples['users'];
        
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Add data
        $rowIndex = 1;
        foreach ($data as $row) {
            $worksheet->fromArray($row, null, 'A' . $rowIndex);
            $rowIndex++;
        }
        
        // Style headers
        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($data[0])) . '1';
        $worksheet->getStyle($headerRange)->getFont()->setBold(true);
        
        // Auto-size columns
        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($data[0]))) as $column) {
            $worksheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Save to temporary file and return content
        $tempFile = tempnam(sys_get_temp_dir(), 'sample_excel_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);
        
        $content = file_get_contents($tempFile);
        unlink($tempFile);
        
        return $content;
    }
}