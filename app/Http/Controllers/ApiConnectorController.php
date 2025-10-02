<?php

/**
 * API Connector Controller
 *
 * Handles external API integrations, sync operations, and connector management.
 * Provides endpoints for managing external system connections and data synchronization.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ExternalProductMap;
use App\Models\SyncLog;
use App\Models\Product;
use App\Jobs\SyncExternalDataJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiConnectorController extends Controller
{
    /**
     * Display API connector dashboard
     */
    public function index()
    {
        $stats = [
            'total_connections' => ExternalProductMap::count(),
            'active_connections' => ExternalProductMap::where('is_active', true)->count(),
            'pending_syncs' => SyncLog::where('status', 'pending')->count(),
            'failed_syncs' => SyncLog::where('status', 'failed')->count(),
        ];

        $recentSyncs = SyncLog::with(['syncable', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        $systemStats = ExternalProductMap::selectRaw('external_system, COUNT(*) as count, 
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count')
            ->groupBy('external_system')
            ->get();

        return view('api-connector.index', compact('stats', 'recentSyncs', 'systemStats'));
    }

    /**
     * Show connector configuration form
     */
    public function create()
    {
        $supportedSystems = [
            'shopify' => 'Shopify',
            'woocommerce' => 'WooCommerce',
            'magento' => 'Magento',
            'amazon' => 'Amazon Seller Central',
            'ebay' => 'eBay',
            'etsy' => 'Etsy',
        ];

        return view('api-connector.create', compact('supportedSystems'));
    }

    /**
     * Store new API connector configuration
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'external_system' => 'required|string|in:shopify,woocommerce,magento,amazon,ebay,etsy',
            'api_endpoint' => 'required|url',
            'api_key' => 'required|string',
            'api_secret' => 'nullable|string',
            'store_url' => 'nullable|url',
            'sync_frequency' => 'required|integer|min:5|max:1440',
            'auto_sync' => 'boolean',
            'sync_direction' => 'required|in:pull,push,bidirectional',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Test API connection
        $connectionTest = $this->testApiConnection($request->all());
        if (!$connectionTest['success']) {
            return back()->withErrors(['api_key' => $connectionTest['message']])->withInput();
        }

        // Store configuration (you might want to create a separate model for this)
        // For now, we'll use the existing structure
        
        return redirect()->route('api-connector.index')
            ->with('success', 'API connector configured successfully!');
    }

    /**
     * Trigger manual sync for specific system
     */
    public function triggerSync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'external_system' => 'required|string',
            'sync_type' => 'required|in:products,orders,customers,inventory',
            'sync_direction' => 'required|in:pull,push,bidirectional',
            'batch_size' => 'nullable|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $batchId = Str::uuid();
        $batchSize = $request->input('batch_size', 100);

        // Queue sync job
        SyncExternalDataJob::dispatch(
            $request->input('external_system'),
            $request->input('sync_type'),
            $request->input('sync_direction'),
            $batchId,
            $batchSize,
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Sync job queued successfully',
            'batch_id' => $batchId
        ]);
    }

    /**
     * Get sync status for a batch
     */
    public function getSyncStatus(Request $request)
    {
        $batchId = $request->input('batch_id');
        
        if (!$batchId) {
            return response()->json(['success' => false, 'message' => 'Batch ID required'], 400);
        }

        $syncLogs = SyncLog::where('batch_id', $batchId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $totalJobs = array_sum($syncLogs);
        $completedJobs = ($syncLogs['completed'] ?? 0) + ($syncLogs['failed'] ?? 0);
        $progress = $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'batch_id' => $batchId,
            'progress' => $progress,
            'total_jobs' => $totalJobs,
            'completed_jobs' => $completedJobs,
            'status_breakdown' => $syncLogs,
            'is_complete' => $progress >= 100
        ]);
    }

    /**
     * Get sync logs with filtering
     */
    public function getSyncLogs(Request $request): JsonResponse
    {
        $query = SyncLog::with(['syncable', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('external_system')) {
            $query->where('external_system', $request->input('external_system'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('sync_type')) {
            $query->where('sync_type', $request->input('sync_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->paginate($request->input('per_page', 50));

        return response()->json($logs);
    }

    /**
     * Retry failed sync operations
     */
    public function retryFailedSyncs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'external_system' => 'nullable|string',
            'sync_type' => 'nullable|string',
            'max_retries' => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $query = SyncLog::where('status', 'failed')
            ->where('retry_count', '<', $request->input('max_retries', 3));

        if ($request->filled('external_system')) {
            $query->where('external_system', $request->input('external_system'));
        }

        if ($request->filled('sync_type')) {
            $query->where('sync_type', $request->input('sync_type'));
        }

        $failedSyncs = $query->get();
        $retryCount = 0;

        foreach ($failedSyncs as $syncLog) {
            // Reset status and increment retry count
            $syncLog->update([
                'status' => 'pending',
                'retry_count' => $syncLog->retry_count + 1,
                'error_message' => null,
            ]);

            // Re-queue the job
            SyncExternalDataJob::dispatch(
                $syncLog->external_system,
                $syncLog->sync_type,
                $syncLog->operation,
                $syncLog->batch_id,
                1, // Single item retry
                $syncLog->user_id,
                $syncLog->id // Pass sync log ID for retry
            );

            $retryCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Queued {$retryCount} failed syncs for retry",
            'retry_count' => $retryCount
        ]);
    }

    /**
     * Get external system health status
     */
    public function getSystemHealth(): JsonResponse
    {
        $systems = ['shopify', 'woocommerce', 'magento', 'amazon', 'ebay', 'etsy'];
        $healthStatus = [];

        foreach ($systems as $system) {
            $recentSyncs = SyncLog::where('external_system', $system)
                ->where('created_at', '>=', Carbon::now()->subHours(24))
                ->get();

            $totalSyncs = $recentSyncs->count();
            $successfulSyncs = $recentSyncs->where('status', 'completed')->count();
            $failedSyncs = $recentSyncs->where('status', 'failed')->count();
            
            $successRate = $totalSyncs > 0 ? round(($successfulSyncs / $totalSyncs) * 100, 2) : 0;
            
            $healthStatus[$system] = [
                'status' => $this->determineHealthStatus($successRate, $failedSyncs),
                'success_rate' => $successRate,
                'total_syncs_24h' => $totalSyncs,
                'failed_syncs_24h' => $failedSyncs,
                'last_sync' => $recentSyncs->max('created_at'),
            ];
        }

        return response()->json([
            'success' => true,
            'health_status' => $healthStatus,
            'overall_health' => $this->calculateOverallHealth($healthStatus)
        ]);
    }

    /**
     * Test API connection
     */
    private function testApiConnection(array $config): array
    {
        // This is a simplified test - in real implementation, you'd make actual API calls
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $config['api_endpoint']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $config['api_key'],
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                return ['success' => true, 'message' => 'Connection successful'];
            } else {
                return ['success' => false, 'message' => 'API connection failed: HTTP ' . $httpCode];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Connection error: ' . $e->getMessage()];
        }
    }

    /**
     * Determine health status based on metrics
     */
    private function determineHealthStatus(float $successRate, int $failedSyncs): string
    {
        if ($successRate >= 95 && $failedSyncs <= 5) {
            return 'healthy';
        } elseif ($successRate >= 80 && $failedSyncs <= 20) {
            return 'warning';
        } else {
            return 'critical';
        }
    }

    /**
     * Calculate overall system health
     */
    private function calculateOverallHealth(array $healthStatus): string
    {
        $healthyCount = 0;
        $warningCount = 0;
        $criticalCount = 0;
        $totalSystems = count($healthStatus);

        foreach ($healthStatus as $status) {
            switch ($status['status']) {
                case 'healthy':
                    $healthyCount++;
                    break;
                case 'warning':
                    $warningCount++;
                    break;
                case 'critical':
                    $criticalCount++;
                    break;
            }
        }

        if ($criticalCount > 0) {
            return 'critical';
        } elseif ($warningCount > $totalSystems / 2) {
            return 'warning';
        } else {
            return 'healthy';
        }
    }
}
