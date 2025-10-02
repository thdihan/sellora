<?php

namespace App\Jobs;

use App\Models\ImportJob;
use App\Models\ImportItem;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class ProcessImportJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $timeout = 3600; // 1 hour
    public $tries = 3;

    protected ImportJob $importJob;

    /**
     * Create a new job instance.
     */
    public function __construct(ImportJob $importJob)
    {
        $this->importJob = $importJob;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->importJob->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            AuditLog::log('import_started', 'ImportJob', $this->importJob->id, [
                'file_name' => $this->importJob->file_name,
                'module' => $this->importJob->module,
            ]);

            $this->processFile();

            $this->importJob->update([
                'status' => 'completed',
                'completed_at' => now(),
                'progress' => 100,
            ]);

            AuditLog::log('import_completed', 'ImportJob', $this->importJob->id, [
                'total_items' => $this->importJob->total_items,
                'processed_items' => $this->importJob->processed_items,
                'failed_items' => $this->importJob->failed_items,
            ]);

        } catch (Exception $e) {
            $this->handleFailure($e);
        }
    }

    protected function processFile(): void
    {
        $filePath = Storage::path($this->importJob->file_path);
        $extension = pathinfo($this->importJob->file_name, PATHINFO_EXTENSION);

        switch (strtolower($extension)) {
            case 'csv':
                $this->processCsv($filePath);
                break;
            case 'xlsx':
            case 'xls':
                $this->processExcel($filePath);
                break;
            case 'sql':
                $this->processSql($filePath);
                break;
            default:
                throw new Exception('Unsupported file format: ' . $extension);
        }
    }

    protected function processCsv(string $filePath): void
    {
        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);
        
        $records = $csv->getRecords();
        $totalRecords = iterator_count($csv->getRecords());
        
        $this->importJob->update(['total_items' => $totalRecords]);
        
        $processed = 0;
        $failed = 0;
        
        foreach ($records as $offset => $record) {
            try {
                $this->processRecord($record, $offset + 1);
                $processed++;
            } catch (Exception $e) {
                $this->createFailedItem($record, $offset + 1, $e->getMessage());
                $failed++;
            }
            
            // Update progress every 100 records
            if (($processed + $failed) % 100 === 0) {
                $progress = (($processed + $failed) / $totalRecords) * 100;
                $this->importJob->update([
                    'progress' => $progress,
                    'processed_items' => $processed,
                    'failed_items' => $failed,
                ]);
            }
        }
        
        $this->importJob->update([
            'processed_items' => $processed,
            'failed_items' => $failed,
        ]);
    }

    protected function processExcel(string $filePath): void
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = $worksheet->toArray();
        
        $headers = array_shift($data); // Remove header row
        $totalRecords = count($data);
        
        $this->importJob->update(['total_items' => $totalRecords]);
        
        $processed = 0;
        $failed = 0;
        
        foreach ($data as $index => $row) {
            try {
                $record = array_combine($headers, $row);
                $this->processRecord($record, $index + 2); // +2 because of header and 0-based index
                $processed++;
            } catch (Exception $e) {
                $this->createFailedItem($row, $index + 2, $e->getMessage());
                $failed++;
            }
            
            // Update progress every 100 records
            if (($processed + $failed) % 100 === 0) {
                $progress = (($processed + $failed) / $totalRecords) * 100;
                $this->importJob->update([
                    'progress' => $progress,
                    'processed_items' => $processed,
                    'failed_items' => $failed,
                ]);
            }
        }
        
        $this->importJob->update([
            'processed_items' => $processed,
            'failed_items' => $failed,
        ]);
    }

    protected function processSql(string $filePath): void
    {
        $sql = file_get_contents($filePath);
        
        // Basic SQL parsing - split by semicolons and filter INSERT statements
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn($stmt) => !empty($stmt) && stripos($stmt, 'INSERT') === 0
        );
        
        $totalRecords = count($statements);
        $this->importJob->update(['total_items' => $totalRecords]);
        
        $processed = 0;
        $failed = 0;
        
        foreach ($statements as $index => $statement) {
            try {
                // Execute SQL statement in a transaction
                DB::transaction(function () use ($statement) {
                    DB::statement($statement);
                });
                
                $this->createSuccessItem($statement, $index + 1);
                $processed++;
            } catch (Exception $e) {
                $this->createFailedItem($statement, $index + 1, $e->getMessage());
                $failed++;
            }
            
            // Update progress every 10 statements
            if (($processed + $failed) % 10 === 0) {
                $progress = (($processed + $failed) / $totalRecords) * 100;
                $this->importJob->update([
                    'progress' => $progress,
                    'processed_items' => $processed,
                    'failed_items' => $failed,
                ]);
            }
        }
        
        $this->importJob->update([
            'processed_items' => $processed,
            'failed_items' => $failed,
        ]);
    }

    protected function processRecord(array $record, int $rowNumber): void
    {
        // Apply field mappings if configured
        if ($this->importJob->field_mappings) {
            $mappedRecord = [];
            foreach ($this->importJob->field_mappings as $sourceField => $targetField) {
                if (isset($record[$sourceField])) {
                    $mappedRecord[$targetField] = $record[$sourceField];
                }
            }
            $record = $mappedRecord;
        }
        
        // Validate required fields
        $this->validateRecord($record);
        
        // Create the record based on module
        $this->createModuleRecord($record);
        
        // Create success import item
        $this->createSuccessItem($record, $rowNumber);
    }

    protected function validateRecord(array $record): void
    {
        $validationRules = $this->getValidationRules();
        
        foreach ($validationRules as $field => $rules) {
            if (in_array('required', $rules) && empty($record[$field])) {
                throw new Exception("Required field '{$field}' is missing or empty");
            }
        }
    }

    protected function getValidationRules(): array
    {
        // Define validation rules based on module
        $rules = [
            'users' => [
                'email' => ['required', 'email'],
                'name' => ['required'],
            ],
            'products' => [
                'name' => ['required'],
                'price' => ['required', 'numeric'],
            ],
            'orders' => [
                'customer_email' => ['required', 'email'],
                'total' => ['required', 'numeric'],
            ],
        ];
        
        return $rules[$this->importJob->module] ?? [];
    }

    protected function createModuleRecord(array $record): void
    {
        switch ($this->importJob->module) {
            case 'users':
                $this->createUser($record);
                break;
            case 'products':
                $this->createProduct($record);
                break;
            case 'orders':
                $this->createOrder($record);
                break;
            default:
                throw new Exception('Unsupported module: ' . $this->importJob->module);
        }
    }

    protected function createUser(array $record): void
    {
        // Implementation would depend on your User model structure
        // This is a placeholder
        DB::table('users')->insert([
            'name' => $record['name'],
            'email' => $record['email'],
            'password' => bcrypt($record['password'] ?? 'defaultpassword'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createProduct(array $record): void
    {
        // Implementation would depend on your Product model structure
        // This is a placeholder
        DB::table('products')->insert([
            'name' => $record['name'],
            'price' => $record['price'],
            'description' => $record['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createOrder(array $record): void
    {
        // Implementation would depend on your Order model structure
        // This is a placeholder
        DB::table('orders')->insert([
            'customer_email' => $record['customer_email'],
            'total' => $record['total'],
            'status' => $record['status'] ?? 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function createSuccessItem($data, int $rowNumber): void
    {
        ImportItem::create([
            'import_job_id' => $this->importJob->id,
            'row_number' => $rowNumber,
            'status' => 'completed',
            'payload' => is_array($data) ? $data : ['sql' => $data],
        ]);
    }

    protected function createFailedItem($data, int $rowNumber, string $errorMessage): void
    {
        ImportItem::create([
            'import_job_id' => $this->importJob->id,
            'row_number' => $rowNumber,
            'status' => 'failed',
            'payload' => is_array($data) ? $data : ['sql' => $data],
            'error_message' => $errorMessage,
        ]);
    }

    protected function handleFailure(Exception $e): void
    {
        Log::error('Import job failed', [
            'import_job_id' => $this->importJob->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        $this->importJob->update([
            'status' => 'failed',
            'error_message' => $e->getMessage(),
            'completed_at' => now(),
        ]);

        AuditLog::log('import_failed', 'ImportJob', $this->importJob->id, [
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
}
