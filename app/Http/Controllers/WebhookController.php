<?php

/**
 * Webhook Controller
 *
 * Handles incoming webhooks from external systems for real-time data synchronization.
 * Processes webhook events and triggers appropriate sync operations.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SyncLog;
use App\Models\Product;
use App\Models\ExternalProductMap;
use App\Jobs\SyncExternalDataJob;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Class WebhookController
 *
 * Handles webhook endpoints for external system integrations
 */
class WebhookController extends Controller
{
    /**
     * Handle Shopify webhooks
     *
     * @param Request $request The incoming webhook request
     * @return JsonResponse
     */
    public function handleShopify(Request $request): JsonResponse
    {
        try {
            // Verify Shopify webhook signature
            if (!$this->verifyShopifyWebhook($request)) {
                Log::warning('Invalid Shopify webhook signature', [
                    'headers' => $request->headers->all(),
                    'ip' => $request->ip()
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $topic = $request->header('X-Shopify-Topic');
            $shopDomain = $request->header('X-Shopify-Shop-Domain');
            $payload = $request->all();

            Log::info('Shopify webhook received', [
                'topic' => $topic,
                'shop_domain' => $shopDomain,
                'payload_keys' => array_keys($payload)
            ]);

            // Process webhook based on topic
            $result = $this->processShopifyWebhook($topic, $shopDomain, $payload);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'processed_items' => $result['processed_items'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('Shopify webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Handle WooCommerce webhooks
     *
     * @param Request $request The incoming webhook request
     * @return JsonResponse
     */
    public function handleWooCommerce(Request $request): JsonResponse
    {
        try {
            // Verify WooCommerce webhook signature
            if (!$this->verifyWooCommerceWebhook($request)) {
                Log::warning('Invalid WooCommerce webhook signature', [
                    'headers' => $request->headers->all(),
                    'ip' => $request->ip()
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $event = $request->header('X-WC-Webhook-Event');
            $resource = $request->header('X-WC-Webhook-Resource');
            $payload = $request->all();

            Log::info('WooCommerce webhook received', [
                'event' => $event,
                'resource' => $resource,
                'payload_keys' => array_keys($payload)
            ]);

            // Process webhook based on event and resource
            $result = $this->processWooCommerceWebhook($event, $resource, $payload);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'processed_items' => $result['processed_items'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('WooCommerce webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Handle generic webhooks from other systems
     *
     * @param Request $request The incoming webhook request
     * @param string $system The external system identifier
     * @return JsonResponse
     */
    public function handleGeneric(Request $request, string $system): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'event_type' => 'required|string',
                'data' => 'required|array',
                'timestamp' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $eventType = $request->input('event_type');
            $data = $request->input('data');
            $timestamp = $request->input('timestamp', now()->toISOString());

            Log::info('Generic webhook received', [
                'system' => $system,
                'event_type' => $eventType,
                'timestamp' => $timestamp,
                'data_keys' => array_keys($data)
            ]);

            // Process generic webhook
            $result = $this->processGenericWebhook($system, $eventType, $data, $timestamp);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'processed_items' => $result['processed_items'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('Generic webhook processing failed', [
                'system' => $system,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Get webhook logs with filtering
     *
     * @param Request $request The request with filter parameters
     * @return JsonResponse
     */
    public function getLogs(Request $request): JsonResponse
    {
        $query = SyncLog::where('sync_type', 'webhook')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('external_system')) {
            $query->where('external_system', $request->input('external_system'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
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
     * Verify Shopify webhook signature
     *
     * @param Request $request The incoming request
     * @return bool
     */
    private function verifyShopifyWebhook(Request $request): bool
    {
        $signature = $request->header('X-Shopify-Hmac-Sha256');
        $webhookSecret = config('services.shopify.webhook_secret');
        
        if (!$signature || !$webhookSecret) {
            return false;
        }

        $calculatedSignature = base64_encode(
            hash_hmac('sha256', $request->getContent(), $webhookSecret, true)
        );

        return hash_equals($signature, $calculatedSignature);
    }

    /**
     * Verify WooCommerce webhook signature
     *
     * @param Request $request The incoming request
     * @return bool
     */
    private function verifyWooCommerceWebhook(Request $request): bool
    {
        $signature = $request->header('X-WC-Webhook-Signature');
        $webhookSecret = config('services.woocommerce.webhook_secret');
        
        if (!$signature || !$webhookSecret) {
            return false;
        }

        $calculatedSignature = base64_encode(
            hash_hmac('sha256', $request->getContent(), $webhookSecret, true)
        );

        return hash_equals($signature, $calculatedSignature);
    }

    /**
     * Process Shopify webhook events
     *
     * @param string $topic The webhook topic
     * @param string $shopDomain The shop domain
     * @param array $payload The webhook payload
     * @return array
     */
    private function processShopifyWebhook(string $topic, string $shopDomain, array $payload): array
    {
        $processedItems = 0;
        $batchId = Str::uuid();

        switch ($topic) {
            case 'products/create':
            case 'products/update':
                $this->processProductWebhook('shopify', $payload, $batchId);
                $processedItems = 1;
                break;

            case 'orders/create':
            case 'orders/updated':
                $this->processOrderWebhook('shopify', $payload, $batchId);
                $processedItems = 1;
                break;

            case 'inventory_levels/update':
                $this->processInventoryWebhook('shopify', $payload, $batchId);
                $processedItems = 1;
                break;

            default:
                Log::info('Unhandled Shopify webhook topic', ['topic' => $topic]);
                break;
        }

        return ['processed_items' => $processedItems];
    }

    /**
     * Process WooCommerce webhook events
     *
     * @param string $event The webhook event
     * @param string $resource The webhook resource
     * @param array $payload The webhook payload
     * @return array
     */
    private function processWooCommerceWebhook(string $event, string $resource, array $payload): array
    {
        $processedItems = 0;
        $batchId = Str::uuid();

        if ($resource === 'product' && in_array($event, ['created', 'updated'])) {
            $this->processProductWebhook('woocommerce', $payload, $batchId);
            $processedItems = 1;
        } elseif ($resource === 'order' && in_array($event, ['created', 'updated'])) {
            $this->processOrderWebhook('woocommerce', $payload, $batchId);
            $processedItems = 1;
        }

        return ['processed_items' => $processedItems];
    }

    /**
     * Process generic webhook events
     *
     * @param string $system The external system
     * @param string $eventType The event type
     * @param array $data The event data
     * @param string $timestamp The event timestamp
     * @return array
     */
    private function processGenericWebhook(string $system, string $eventType, array $data, string $timestamp): array
    {
        $processedItems = 0;
        $batchId = Str::uuid();

        // Create sync log for webhook event
        SyncLog::create([
            'external_system' => $system,
            'sync_type' => 'webhook',
            'operation' => $eventType,
            'batch_id' => $batchId,
            'status' => 'processing',
            'external_id' => $data['id'] ?? null,
            'data' => $data,
            'user_id' => null, // Webhook events don't have a user
        ]);

        // Process based on event type
        if (str_contains($eventType, 'product')) {
            $this->processProductWebhook($system, $data, $batchId);
            $processedItems = 1;
        } elseif (str_contains($eventType, 'order')) {
            $this->processOrderWebhook($system, $data, $batchId);
            $processedItems = 1;
        } elseif (str_contains($eventType, 'inventory')) {
            $this->processInventoryWebhook($system, $data, $batchId);
            $processedItems = 1;
        }

        return ['processed_items' => $processedItems];
    }

    /**
     * Process product webhook events
     *
     * @param string $system The external system
     * @param array $payload The product data
     * @param string $batchId The batch ID
     * @return void
     */
    private function processProductWebhook(string $system, array $payload, string $batchId): void
    {
        // Queue sync job for product
        SyncExternalDataJob::dispatch(
            $system,
            'products',
            'pull',
            $batchId,
            1,
            null, // No user for webhook
            null,
            $payload['id'] ?? null
        );

        Log::info('Product webhook queued for sync', [
            'system' => $system,
            'product_id' => $payload['id'] ?? 'unknown',
            'batch_id' => $batchId
        ]);
    }

    /**
     * Process order webhook events
     *
     * @param string $system The external system
     * @param array $payload The order data
     * @param string $batchId The batch ID
     * @return void
     */
    private function processOrderWebhook(string $system, array $payload, string $batchId): void
    {
        // Queue sync job for order
        SyncExternalDataJob::dispatch(
            $system,
            'orders',
            'pull',
            $batchId,
            1,
            null, // No user for webhook
            null,
            $payload['id'] ?? null
        );

        Log::info('Order webhook queued for sync', [
            'system' => $system,
            'order_id' => $payload['id'] ?? 'unknown',
            'batch_id' => $batchId
        ]);
    }

    /**
     * Process inventory webhook events
     *
     * @param string $system The external system
     * @param array $payload The inventory data
     * @param string $batchId The batch ID
     * @return void
     */
    private function processInventoryWebhook(string $system, array $payload, string $batchId): void
    {
        // Queue sync job for inventory
        SyncExternalDataJob::dispatch(
            $system,
            'inventory',
            'pull',
            $batchId,
            1,
            null, // No user for webhook
            null,
            $payload['inventory_item_id'] ?? $payload['id'] ?? null
        );

        Log::info('Inventory webhook queued for sync', [
            'system' => $system,
            'item_id' => $payload['inventory_item_id'] ?? $payload['id'] ?? 'unknown',
            'batch_id' => $batchId
        ]);
    }
}
