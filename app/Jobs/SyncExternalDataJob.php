<?php

/**
 * Sync External Data Job
 *
 * Handles synchronization of data between Sellora and external systems.
 * Processes sync operations in background queues for better performance.
 *
 * @category Job
 * @package  App\Jobs
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Jobs;

use App\Models\SyncLog;
use App\Models\Product;
use App\Models\ExternalProductMap;
use App\Services\External\ExternalApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

/**
 * Class SyncExternalDataJob
 *
 * Background job for synchronizing data with external systems
 */
class SyncExternalDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * External system identifier
     *
     * @var string
     */
    protected $externalSystem;

    /**
     * Type of sync operation
     *
     * @var string
     */
    protected $syncType;

    /**
     * Direction of sync
     *
     * @var string
     */
    protected $syncDirection;

    /**
     * Batch identifier
     *
     * @var string
     */
    protected $batchId;

    /**
     * Batch size for processing
     *
     * @var int
     */
    protected $batchSize;

    /**
     * User ID who initiated the sync
     *
     * @var int|null
     */
    protected $userId;

    /**
     * Sync log ID for retry operations
     *
     * @var int|null
     */
    protected $syncLogId;

    /**
     * Specific external ID to sync
     *
     * @var string|null
     */
    protected $externalId;

    /**
     * Create a new job instance.
     *
     * @param string      $externalSystem The external system identifier
     * @param string      $syncType       The type of sync operation
     * @param string      $syncDirection  The direction of sync
     * @param string      $batchId        The batch identifier
     * @param int         $batchSize      The batch size for processing
     * @param int|null    $userId         The user ID who initiated the sync
     * @param int|null    $syncLogId      The sync log ID for retry operations
     * @param string|null $externalId     The specific external ID to sync
     */
    public function __construct(
        string $externalSystem,
        string $syncType,
        string $syncDirection,
        string $batchId,
        int $batchSize = 100,
        ?int $userId = null,
        ?int $syncLogId = null,
        ?string $externalId = null
    ) {
        $this->externalSystem = $externalSystem;
        $this->syncType = $syncType;
        $this->syncDirection = $syncDirection;
        $this->batchId = $batchId;
        $this->batchSize = $batchSize;
        $this->userId = $userId;
        $this->syncLogId = $syncLogId;
        $this->externalId = $externalId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            Log::info('Starting sync job', [
                'external_system' => $this->externalSystem,
                'sync_type' => $this->syncType,
                'sync_direction' => $this->syncDirection,
                'batch_id' => $this->batchId,
                'batch_size' => $this->batchSize,
                'external_id' => $this->externalId
            ]);

            // Create or update sync log
            $syncLog = $this->createOrUpdateSyncLog();

            // Initialize external API service
            $apiService = new ExternalApiService($this->externalSystem);

            // Process sync based on type and direction
            $result = $this->procesSync($apiService, $syncLog);

            // Update sync log with results
            $this->updateSyncLogSuccess($syncLog, $result);

            Log::info('Sync job completed successfully', [
                'sync_log_id' => $syncLog->id,
                'processed_items' => $result['processed_items'] ?? 0,
                'batch_id' => $this->batchId
            ]);

        } catch (Exception $e) {
            Log::error('Sync job failed', [
                'external_system' => $this->externalSystem,
                'sync_type' => $this->syncType,
                'batch_id' => $this->batchId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update sync log with error
            if (isset($syncLog)) {
                $this->updateSyncLogError($syncLog, $e);
            }

            // Re-throw exception to trigger job retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param Exception $exception The exception that caused the failure
     * @return void
     */
    public function failed(Exception $exception): void
    {
        Log::error('Sync job permanently failed', [
            'external_system' => $this->externalSystem,
            'sync_type' => $this->syncType,
            'batch_id' => $this->batchId,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage()
        ]);

        // Update sync log to failed status
        if ($this->syncLogId) {
            $syncLog = SyncLog::find($this->syncLogId);
            if ($syncLog) {
                $syncLog->update([
                    'status' => 'failed',
                    'error_message' => $exception->getMessage(),
                    'completed_at' => now(),
                ]);
            }
        }
    }

    /**
     * Create or update sync log entry
     *
     * @return SyncLog
     */
    private function createOrUpdateSyncLog(): SyncLog
    {
        if ($this->syncLogId) {
            // Update existing sync log for retry
            $syncLog = SyncLog::findOrFail($this->syncLogId);
            $syncLog->update([
                'status' => 'processing',
                'started_at' => now(),
                'error_message' => null,
            ]);
            return $syncLog;
        }

        // Create new sync log
        return SyncLog::create([
            'external_system' => $this->externalSystem,
            'sync_type' => $this->syncType,
            'operation' => $this->syncDirection,
            'batch_id' => $this->batchId,
            'status' => 'processing',
            'external_id' => $this->externalId,
            'user_id' => $this->userId,
            'started_at' => now(),
        ]);
    }

    /**
     * Process the sync operation
     *
     * @param ExternalApiService $apiService The API service instance
     * @param SyncLog            $syncLog    The sync log entry
     * @return array
     */
    private function procesSync(ExternalApiService $apiService, SyncLog $syncLog): array
    {
        switch ($this->syncType) {
            case 'products':
                return $this->syncProducts($apiService, $syncLog);
            case 'orders':
                return $this->syncOrders($apiService, $syncLog);
            case 'customers':
                return $this->syncCustomers($apiService, $syncLog);
            case 'inventory':
                return $this->syncInventory($apiService, $syncLog);
            default:
                throw new Exception("Unsupported sync type: {$this->syncType}");
        }
    }

    /**
     * Sync products with external system
     *
     * @param ExternalApiService $apiService The API service instance
     * @param SyncLog            $syncLog    The sync log entry
     * @return array
     */
    private function syncProducts(ExternalApiService $apiService, SyncLog $syncLog): array
    {
        $processedItems = 0;
        $errors = [];

        if ($this->syncDirection === 'pull' || $this->syncDirection === 'bidirectional') {
            // Pull products from external system
            if ($this->externalId) {
                // Sync specific product
                $externalProduct = $apiService->getProduct($this->externalId);
                if ($externalProduct) {
                    $this->importProduct($externalProduct);
                    $processedItems++;
                }
            } else {
                // Sync batch of products
                $externalProducts = $apiService->getProducts($this->batchSize);
                foreach ($externalProducts as $externalProduct) {
                    try {
                        $this->importProduct($externalProduct);
                        $processedItems++;
                    } catch (Exception $e) {
                        $errors[] = "Product {$externalProduct['id']}: {$e->getMessage()}";
                    }
                }
            }
        }

        if ($this->syncDirection === 'push' || $this->syncDirection === 'bidirectional') {
            // Push products to external system
            $localProducts = Product::whereDoesntHave('externalMaps', function ($query) {
                $query->where('external_system', $this->externalSystem);
            })->limit($this->batchSize)->get();

            foreach ($localProducts as $product) {
                try {
                    $externalProduct = $apiService->createProduct($product->toArray());
                    if ($externalProduct) {
                        // Create external mapping
                        ExternalProductMap::create([
                            'product_id' => $product->id,
                            'external_system' => $this->externalSystem,
                            'external_id' => $externalProduct['id'],
                            'external_url' => $externalProduct['url'] ?? null,
                            'is_active' => true,
                            'last_synced_at' => now(),
                        ]);
                        $processedItems++;
                    }
                } catch (Exception $e) {
                    $errors[] = "Product {$product->id}: {$e->getMessage()}";
                }
            }
        }

        return [
            'processed_items' => $processedItems,
            'errors' => $errors,
        ];
    }

    /**
     * Sync orders with external system
     *
     * @param ExternalApiService $apiService The API service instance
     * @param SyncLog            $syncLog    The sync log entry
     * @return array
     */
    private function syncOrders(ExternalApiService $apiService, SyncLog $syncLog): array
    {
        $processedItems = 0;
        $errors = [];

        if ($this->syncDirection === 'pull' || $this->syncDirection === 'bidirectional') {
            // Pull orders from external system
            if ($this->externalId) {
                $externalOrder = $apiService->getOrder($this->externalId);
                if ($externalOrder) {
                    $this->importOrder($externalOrder);
                    $processedItems++;
                }
            } else {
                $externalOrders = $apiService->getOrders($this->batchSize);
                foreach ($externalOrders as $externalOrder) {
                    try {
                        $this->importOrder($externalOrder);
                        $processedItems++;
                    } catch (Exception $e) {
                        $errors[] = "Order {$externalOrder['id']}: {$e->getMessage()}";
                    }
                }
            }
        }

        return [
            'processed_items' => $processedItems,
            'errors' => $errors,
        ];
    }

    /**
     * Sync customers with external system
     *
     * @param ExternalApiService $apiService The API service instance
     * @param SyncLog            $syncLog    The sync log entry
     * @return array
     */
    private function syncCustomers(ExternalApiService $apiService, SyncLog $syncLog): array
    {
        $processedItems = 0;
        $errors = [];

        // Implementation for customer sync
        // This would be similar to products but for customer data

        return [
            'processed_items' => $processedItems,
            'errors' => $errors,
        ];
    }

    /**
     * Sync inventory with external system
     *
     * @param ExternalApiService $apiService The API service instance
     * @param SyncLog            $syncLog    The sync log entry
     * @return array
     */
    private function syncInventory(ExternalApiService $apiService, SyncLog $syncLog): array
    {
        $processedItems = 0;
        $errors = [];

        // Implementation for inventory sync
        // This would update stock levels based on external system data

        return [
            'processed_items' => $processedItems,
            'errors' => $errors,
        ];
    }

    /**
     * Import product from external system
     *
     * @param array $externalProduct The external product data
     * @return Product
     */
    private function importProduct(array $externalProduct): Product
    {
        // Check if product already exists
        $existingMap = ExternalProductMap::where('external_system', $this->externalSystem)
            ->where('external_id', $externalProduct['id'])
            ->first();

        if ($existingMap) {
            // Update existing product
            $product = $existingMap->product;
            $product->update([
                'name' => $externalProduct['name'] ?? $product->name,
                'description' => $externalProduct['description'] ?? $product->description,
                'price' => $externalProduct['price'] ?? $product->price,
                'sku' => $externalProduct['sku'] ?? $product->sku,
            ]);

            // Update mapping
            $existingMap->update([
                'last_synced_at' => now(),
                'is_active' => true,
            ]);

            return $product;
        }

        // Create new product
        $product = Product::create([
            'name' => $externalProduct['name'],
            'description' => $externalProduct['description'] ?? '',
            'price' => $externalProduct['price'] ?? 0,
            'sku' => $externalProduct['sku'] ?? '',
            'status' => 'active',
        ]);

        // Create external mapping
        ExternalProductMap::create([
            'product_id' => $product->id,
            'external_system' => $this->externalSystem,
            'external_id' => $externalProduct['id'],
            'external_url' => $externalProduct['url'] ?? null,
            'is_active' => true,
            'last_synced_at' => now(),
        ]);

        return $product;
    }

    /**
     * Import order from external system
     *
     * @param array $externalOrder The external order data
     * @return void
     */
    private function importOrder(array $externalOrder): void
    {
        // Implementation for importing orders
        // This would create/update orders in the local system
        Log::info('Order import placeholder', ['order_id' => $externalOrder['id']]);
    }

    /**
     * Update sync log with success status
     *
     * @param SyncLog $syncLog The sync log entry
     * @param array   $result  The sync result
     * @return void
     */
    private function updateSyncLogSuccess(SyncLog $syncLog, array $result): void
    {
        $syncLog->update([
            'status' => 'completed',
            'completed_at' => now(),
            'data' => $result,
            'error_message' => null,
        ]);
    }

    /**
     * Update sync log with error status
     *
     * @param SyncLog   $syncLog   The sync log entry
     * @param Exception $exception The exception that occurred
     * @return void
     */
    private function updateSyncLogError(SyncLog $syncLog, Exception $exception): void
    {
        $syncLog->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_message' => $exception->getMessage(),
            'retry_count' => $syncLog->retry_count + 1,
        ]);
    }
}
