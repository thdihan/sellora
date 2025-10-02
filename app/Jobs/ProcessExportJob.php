<?php

namespace App\Jobs;

use App\Models\ExportJob;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class ProcessExportJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $timeout = 3600; // 1 hour
    public $tries = 3;

    protected ExportJob $exportJob;

    /**
     * Create a new job instance.
     */
    public function __construct(ExportJob $exportJob)
    {
        $this->exportJob = $exportJob;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->exportJob->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            AuditLog::log('export_started', 'ExportJob', $this->exportJob->id, [
                'modules' => $this->exportJob->modules,
                'format' => $this->exportJob->format,
                'scope' => $this->exportJob->scope,
            ]);

            $this->processExport();

            $this->exportJob->update([
                'status' => 'completed',
                'completed_at' => now(),
                'progress' => 100,
            ]);

            AuditLog::log('export_completed', 'ExportJob', $this->exportJob->id, [
                'file_path' => $this->exportJob->file_path,
                'file_size' => $this->exportJob->file_size,
            ]);

        } catch (Exception $e) {
            $this->handleFailure($e);
        }
    }

    protected function processExport(): void
    {
        $data = $this->collectData();
        
        switch ($this->exportJob->format) {
            case 'csv':
                $this->exportToCsv($data);
                break;
            case 'xlsx':
                $this->exportToExcel($data);
                break;
            case 'sql':
                $this->exportToSql($data);
                break;
            case 'json':
                $this->exportToJson($data);
                break;
            default:
                throw new Exception('Unsupported export format: ' . $this->exportJob->format);
        }
    }

    protected function collectData(): array
    {
        $data = [];
        $modules = $this->exportJob->modules;
        $totalModules = count($modules);
        $processedModules = 0;

        foreach ($modules as $module) {
            $moduleData = $this->getModuleData($module);
            $data[$module] = $moduleData;
            
            $processedModules++;
            $progress = ($processedModules / $totalModules) * 50; // 50% for data collection
            
            $this->exportJob->update(['progress' => $progress]);
        }

        return $data;
    }

    protected function getModuleData(string $module): array
    {
        $query = $this->buildModuleQuery($module);
        
        // Apply date range filter if scope is partial
        if ($this->exportJob->scope === 'partial' && $this->exportJob->date_range) {
            $dateRange = $this->exportJob->date_range;
            if (isset($dateRange['start']) && isset($dateRange['end'])) {
                $query->whereBetween('created_at', [
                    $dateRange['start'],
                    $dateRange['end']
                ]);
            }
        }
        
        return $query->get()->toArray();
    }

    protected function buildModuleQuery(string $module)
    {
        switch ($module) {
            case 'users':
                return DB::table('users')
                    ->select('id', 'name', 'email', 'created_at', 'updated_at');
            
            case 'products':
                return DB::table('products')
                    ->select('id', 'name', 'description', 'price', 'created_at', 'updated_at');
            
            case 'orders':
                return DB::table('orders')
                    ->select('id', 'customer_email', 'total', 'status', 'created_at', 'updated_at');
            
            case 'categories':
                return DB::table('categories')
                    ->select('id', 'name', 'description', 'created_at', 'updated_at');
            
            case 'inventory':
                return DB::table('inventory')
                    ->select('id', 'product_id', 'quantity', 'location', 'created_at', 'updated_at');
            
            default:
                throw new Exception('Unsupported module: ' . $module);
        }
    }

    protected function exportToCsv(array $data): void
    {
        $fileName = 'export_' . $this->exportJob->id . '_' . date('Y-m-d_H-i-s') . '.csv';
        $filePath = 'exports/' . $fileName;
        
        $csv = Writer::createFromString('');
        
        foreach ($data as $module => $records) {
            if (empty($records)) continue;
            
            // Add module header
            $csv->insertOne(['=== ' . strtoupper($module) . ' ===']);
            
            // Add column headers
            $headers = array_keys((array) $records[0]);
            $csv->insertOne($headers);
            
            // Add data rows
            foreach ($records as $record) {
                $csv->insertOne(array_values((array) $record));
            }
            
            // Add empty row between modules
            $csv->insertOne([]);
        }
        
        Storage::put($filePath, $csv->toString());
        
        $this->exportJob->update([
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => Storage::size($filePath),
            'progress' => 100,
        ]);
    }

    protected function exportToExcel(array $data): void
    {
        $fileName = 'export_' . $this->exportJob->id . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filePath = 'exports/' . $fileName;
        
        $spreadsheet = new Spreadsheet();
        $sheetIndex = 0;
        
        foreach ($data as $module => $records) {
            if (empty($records)) continue;
            
            if ($sheetIndex > 0) {
                $spreadsheet->createSheet();
            }
            
            $worksheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
            $worksheet->setTitle(ucfirst($module));
            
            // Add headers
            $headers = array_keys((array) $records[0]);
            $worksheet->fromArray($headers, null, 'A1');
            
            // Add data
            $rowData = [];
            foreach ($records as $record) {
                $rowData[] = array_values((array) $record);
            }
            $worksheet->fromArray($rowData, null, 'A2');
            
            $sheetIndex++;
        }
        
        // Remove default sheet if we created others
        if ($sheetIndex > 0) {
            $spreadsheet->removeSheetByIndex($sheetIndex);
        }
        
        $writer = new Xlsx($spreadsheet);
        $tempPath = storage_path('app/' . $filePath);
        
        // Ensure directory exists
        $directory = dirname($tempPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $writer->save($tempPath);
        
        $this->exportJob->update([
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => filesize($tempPath),
            'progress' => 100,
        ]);
    }

    protected function exportToSql(array $data): void
    {
        $fileName = 'export_' . $this->exportJob->id . '_' . date('Y-m-d_H-i-s') . '.sql';
        $filePath = 'exports/' . $fileName;
        
        $sql = "-- Sellora Data Export\n";
        $sql .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($data as $module => $records) {
            if (empty($records)) continue;
            
            $tableName = $this->getTableName($module);
            $sql .= "-- Data for table: {$tableName}\n";
            
            foreach ($records as $record) {
                $record = (array) $record;
                $columns = implode(', ', array_keys($record));
                $values = implode(', ', array_map(function($value) {
                    return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                }, array_values($record)));
                
                $sql .= "INSERT INTO {$tableName} ({$columns}) VALUES ({$values});\n";
            }
            
            $sql .= "\n";
        }
        
        Storage::put($filePath, $sql);
        
        $this->exportJob->update([
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => Storage::size($filePath),
            'progress' => 100,
        ]);
    }

    protected function exportToJson(array $data): void
    {
        $fileName = 'export_' . $this->exportJob->id . '_' . date('Y-m-d_H-i-s') . '.json';
        $filePath = 'exports/' . $fileName;
        
        $exportData = [
            'export_info' => [
                'id' => $this->exportJob->id,
                'generated_at' => now()->toISOString(),
                'scope' => $this->exportJob->scope,
                'modules' => $this->exportJob->modules,
            ],
            'data' => $data,
        ];
        
        Storage::put($filePath, json_encode($exportData, JSON_PRETTY_PRINT));
        
        $this->exportJob->update([
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => Storage::size($filePath),
            'progress' => 100,
        ]);
    }

    protected function getTableName(string $module): string
    {
        $tableMap = [
            'users' => 'users',
            'products' => 'products',
            'orders' => 'orders',
            'categories' => 'categories',
            'inventory' => 'inventory',
        ];
        
        return $tableMap[$module] ?? $module;
    }

    protected function handleFailure(Exception $e): void
    {
        Log::error('Export job failed', [
            'export_job_id' => $this->exportJob->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        $this->exportJob->update([
            'status' => 'failed',
            'error_message' => $e->getMessage(),
            'completed_at' => now(),
        ]);

        AuditLog::log('export_failed', 'ExportJob', $this->exportJob->id, [
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
}
