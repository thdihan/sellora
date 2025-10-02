<?php

/**
 * API Routes
 *
 * Location tracking API routes for pharma sales force app.
 * All routes require authentication via Sanctum tokens.
 *
 * @category Routes
 * @package  Sellora
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationTrackingController;
use App\Http\Controllers\ApiConnectorController;
use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned to the "api" middleware group. Enjoy building your API!
|
*/

// Default user route
Route::middleware('auth:sanctum')->get(
    '/user',
    function (Request $request) {
        return $request->user();
    }
);

/*
|--------------------------------------------------------------------------
| Location Tracking API Routes
|--------------------------------------------------------------------------
|
| These routes handle real-time location tracking for the pharma sales force.
| All routes require authentication and implement role-based access control.
|
*/

Route::middleware(['auth:sanctum'])->group(
    function () {
        // Store location (POST /api/locations)
        // Rate limited to prevent spam (min 10 seconds between posts)
        Route::post('/locations', [LocationTrackingController::class, 'store'])
            ->name('api.locations.store');
        
        // Get latest locations (GET /api/locations/latest)
        // Role-based: MR sees own, ASM+ see subordinates
        Route::get('/locations/latest', [LocationTrackingController::class, 'latest'])
            ->name('api.locations.latest');
        
        // Get user's location history (GET /api/locations/history)
        // Users can only see their own history
        Route::get('/locations/history', [LocationTrackingController::class, 'history'])
            ->name('api.locations.history');
    }
);

/*
|--------------------------------------------------------------------------
| External API Connector Routes
|--------------------------------------------------------------------------
|
| Routes for managing external API integrations and synchronization.
| Requires authentication and admin privileges.
|
*/

Route::middleware(['auth:sanctum'])->prefix('connector')->group(
    function () {
        // Get connector dashboard data
        Route::get('/dashboard', [ApiConnectorController::class, 'dashboard'])
            ->name('api.connector.dashboard');
        
        // Get connector configuration
        Route::get('/config/{system}', [ApiConnectorController::class, 'getConfig'])
            ->name('api.connector.config');
        
        // Update connector configuration
        Route::put('/config/{system}', [ApiConnectorController::class, 'updateConfig'])
            ->name('api.connector.config.update');
        
        // Trigger sync operation
        Route::post('/sync', [ApiConnectorController::class, 'triggerSync'])
            ->name('api.connector.sync');
        
        // Get sync status
        Route::get('/sync/status/{batchId}', [ApiConnectorController::class, 'getSyncStatus'])
            ->name('api.connector.sync.status');
        
        // Get sync logs
        Route::get('/logs', [ApiConnectorController::class, 'getLogs'])
            ->name('api.connector.logs');
        
        // Retry failed sync
        Route::post('/retry/{syncLogId}', [ApiConnectorController::class, 'retrySync'])
            ->name('api.connector.retry');
        
        // Test API connection
        Route::post('/test/{system}', [ApiConnectorController::class, 'testConnection'])
            ->name('api.connector.test');
        
        // Get system health
        Route::get('/health', [ApiConnectorController::class, 'getSystemHealth'])
            ->name('api.connector.health');
    }
);

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
|
| Routes for handling incoming webhooks from external systems.
| These routes are public and use signature verification for security.
|
*/

Route::prefix('webhooks')->group(
    function () {
        // Shopify webhooks
        Route::post('/shopify/{event}', [WebhookController::class, 'handleShopify'])
            ->name('api.webhooks.shopify');
        
        // WooCommerce webhooks
        Route::post('/woocommerce/{event}', [WebhookController::class, 'handleWooCommerce'])
            ->name('api.webhooks.woocommerce');
        
        // Generic webhook handler
        Route::post('/generic/{system}/{event}', [WebhookController::class, 'handleGeneric'])
            ->name('api.webhooks.generic');
        
        // Get webhook logs
        Route::middleware(['auth:sanctum'])->get('/logs', [WebhookController::class, 'getLogs'])
            ->name('api.webhooks.logs');
    }
);

/*
|--------------------------------------------------------------------------
| Health Check Routes
|--------------------------------------------------------------------------
|
| Simple health check endpoints for monitoring and testing.
|
*/

// API health check
Route::get(
    '/health',
    function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'service' => 'Location Tracking API',
            'version' => '1.0.0'
        ]);
    }
)->name('api.health');

// Database health check
Route::get(
    '/health/database',
    function () {
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            return response()->json([
                'status' => 'ok',
                'database' => 'connected',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'database' => 'disconnected',
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
)->name('api.health.database');

// Import/Export API Routes
Route::middleware('auth:sanctum')->group(
    function () {
        // Import routes
        Route::prefix('imports')->group(
            function () {
                Route::get('/', [App\Http\Controllers\Api\ImportController::class, 'index']);
                Route::post('/', [App\Http\Controllers\Api\ImportController::class, 'store']);
                Route::get('/{job}', [App\Http\Controllers\Api\ImportController::class, 'show']);
                Route::delete('/{job}', [App\Http\Controllers\Api\ImportController::class, 'destroy']);
                
                Route::get('/presets/list', [App\Http\Controllers\Api\ImportController::class, 'presets']);
                Route::post('/presets', [App\Http\Controllers\Api\ImportController::class, 'storePreset']);
            }
        );
        
        // Export routes
        Route::prefix('exports')->group(
            function () {
                Route::get('/', [App\Http\Controllers\Api\ExportController::class, 'index']);
                Route::post('/', [App\Http\Controllers\Api\ExportController::class, 'store']);
                Route::get('/{job}', [App\Http\Controllers\Api\ExportController::class, 'show']);
                Route::get('/{job}/download', [App\Http\Controllers\Api\ExportController::class, 'download']);
                Route::delete('/{job}', [App\Http\Controllers\Api\ExportController::class, 'destroy']);
            }
        );
        
        // Customer API Routes
        Route::prefix('customers')->group(
            function () {
                Route::get('/search', [App\Http\Controllers\CustomerController::class, 'search']);
                Route::get('/{customer}/summary', [App\Http\Controllers\CustomerController::class, 'summary']);
            }
        );
        
        // Product API Routes
        Route::prefix('products')->group(
            function () {
                Route::get('/search', [App\Http\Controllers\ProductController::class, 'apiSearch']);
                
                // Product Synchronization Routes
                Route::post('/{product}/sync', [App\Http\Controllers\Api\ProductSyncController::class, 'syncProduct'])
                    ->name('api.products.sync');
                Route::put('/{product}/update-with-sync', [App\Http\Controllers\Api\ProductSyncController::class, 'updateWithSync'])
                    ->name('api.products.update-with-sync');
                Route::post('/sync-all', [App\Http\Controllers\Api\ProductSyncController::class, 'syncAllProducts'])
                    ->name('api.products.sync-all');
                Route::get('/{product}/realtime', [App\Http\Controllers\Api\ProductSyncController::class, 'getRealtimeData'])
                    ->name('api.products.realtime');
                Route::get('/sync-status', [App\Http\Controllers\Api\ProductSyncController::class, 'getSyncStatus'])
                    ->name('api.products.sync-status');
            }
        );
        
        // Order Tax Management Routes
        Route::prefix('orders')->group(
            function () {
                Route::get('/tax-options', [App\Http\Controllers\Order\OrderTaxController::class, 'getTaxOptions']);
                Route::get('/{order}/tax-options', [App\Http\Controllers\Order\OrderTaxController::class, 'getTaxOptions']);
                Route::post('/{order}/calculate-taxes', [App\Http\Controllers\Order\OrderTaxController::class, 'calculateTaxes']);
                Route::post('/{order}/save-with-taxes', [App\Http\Controllers\Order\OrderTaxController::class, 'saveWithTaxes']);
            }
        );
        
        // Settings API Routes
        Route::prefix('settings')->group(
            function () {
                Route::get('/tax-rates', [App\Http\Controllers\SettingsController::class, 'getTaxRates']);
            }
        );
    }
);