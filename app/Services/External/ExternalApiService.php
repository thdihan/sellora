<?php

/**
 * External API Service
 *
 * Handles communication with external systems like Shopify, WooCommerce, etc.
 * Provides a unified interface for different external API integrations.
 *
 * @category Service
 * @package  App\Services\External
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Services\External;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * Class ExternalApiService
 *
 * Service for handling external API integrations
 */
class ExternalApiService
{
    /**
     * External system identifier
     *
     * @var string
     */
    protected $externalSystem;

    /**
     * API configuration
     *
     * @var array
     */
    protected $config;

    /**
     * HTTP client timeout
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * Rate limit cache key prefix
     *
     * @var string
     */
    protected $rateLimitPrefix = 'api_rate_limit';

    /**
     * Create a new service instance.
     *
     * @param string $externalSystem The external system identifier
     */
    public function __construct(string $externalSystem)
    {
        $this->externalSystem = $externalSystem;
        $this->config = $this->getApiConfig($externalSystem);
    }

    /**
     * Get products from external system
     *
     * @param int $limit The number of products to retrieve
     * @param int $offset The offset for pagination
     * @return array
     */
    public function getProducts(int $limit = 100, int $offset = 0): array
    {
        try {
            $this->checkRateLimit();

            switch ($this->externalSystem) {
                case 'shopify':
                    return $this->getShopifyProducts($limit, $offset);
                case 'woocommerce':
                    return $this->getWooCommerceProducts($limit, $offset);
                default:
                    throw new Exception("Unsupported external system: {$this->externalSystem}");
            }
        } catch (Exception $e) {
            Log::error('Failed to get products from external system', [
                'external_system' => $this->externalSystem,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get a specific product from external system
     *
     * @param string $externalId The external product ID
     * @return array|null
     */
    public function getProduct(string $externalId): ?array
    {
        try {
            $this->checkRateLimit();

            switch ($this->externalSystem) {
                case 'shopify':
                    return $this->getShopifyProduct($externalId);
                case 'woocommerce':
                    return $this->getWooCommerceProduct($externalId);
                default:
                    throw new Exception("Unsupported external system: {$this->externalSystem}");
            }
        } catch (Exception $e) {
            Log::error('Failed to get product from external system', [
                'external_system' => $this->externalSystem,
                'external_id' => $externalId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Create a product in external system
     *
     * @param array $productData The product data to create
     * @return array|null
     */
    public function createProduct(array $productData): ?array
    {
        try {
            $this->checkRateLimit();

            switch ($this->externalSystem) {
                case 'shopify':
                    return $this->createShopifyProduct($productData);
                case 'woocommerce':
                    return $this->createWooCommerceProduct($productData);
                default:
                    throw new Exception("Unsupported external system: {$this->externalSystem}");
            }
        } catch (Exception $e) {
            Log::error('Failed to create product in external system', [
                'external_system' => $this->externalSystem,
                'product_data' => $productData,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get orders from external system
     *
     * @param int $limit The number of orders to retrieve
     * @param int $offset The offset for pagination
     * @return array
     */
    public function getOrders(int $limit = 100, int $offset = 0): array
    {
        try {
            $this->checkRateLimit();

            switch ($this->externalSystem) {
                case 'shopify':
                    return $this->getShopifyOrders($limit, $offset);
                case 'woocommerce':
                    return $this->getWooCommerceOrders($limit, $offset);
                default:
                    throw new Exception("Unsupported external system: {$this->externalSystem}");
            }
        } catch (Exception $e) {
            Log::error('Failed to get orders from external system', [
                'external_system' => $this->externalSystem,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get a specific order from external system
     *
     * @param string $externalId The external order ID
     * @return array|null
     */
    public function getOrder(string $externalId): ?array
    {
        try {
            $this->checkRateLimit();

            switch ($this->externalSystem) {
                case 'shopify':
                    return $this->getShopifyOrder($externalId);
                case 'woocommerce':
                    return $this->getWooCommerceOrder($externalId);
                default:
                    throw new Exception("Unsupported external system: {$this->externalSystem}");
            }
        } catch (Exception $e) {
            Log::error('Failed to get order from external system', [
                'external_system' => $this->externalSystem,
                'external_id' => $externalId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Test API connection
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            switch ($this->externalSystem) {
                case 'shopify':
                    return $this->testShopifyConnection();
                case 'woocommerce':
                    return $this->testWooCommerceConnection();
                default:
                    return false;
            }
        } catch (Exception $e) {
            Log::error('API connection test failed', [
                'external_system' => $this->externalSystem,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get API configuration for external system
     *
     * @param string $externalSystem The external system identifier
     * @return array
     */
    protected function getApiConfig(string $externalSystem): array
    {
        $configs = [
            'shopify' => [
                'base_url' => config('services.shopify.base_url'),
                'api_key' => config('services.shopify.api_key'),
                'api_secret' => config('services.shopify.api_secret'),
                'access_token' => config('services.shopify.access_token'),
                'version' => config('services.shopify.version', '2023-10'),
            ],
            'woocommerce' => [
                'base_url' => config('services.woocommerce.base_url'),
                'consumer_key' => config('services.woocommerce.consumer_key'),
                'consumer_secret' => config('services.woocommerce.consumer_secret'),
                'version' => config('services.woocommerce.version', 'wc/v3'),
            ],
        ];

        return $configs[$externalSystem] ?? [];
    }

    /**
     * Check rate limit for API calls
     *
     * @return void
     * @throws Exception
     */
    protected function checkRateLimit(): void
    {
        $cacheKey = "{$this->rateLimitPrefix}:{$this->externalSystem}";
        $currentCount = Cache::get($cacheKey, 0);
        $rateLimit = $this->getRateLimit();

        if ($currentCount >= $rateLimit['limit']) {
            throw new Exception("Rate limit exceeded for {$this->externalSystem}");
        }

        Cache::put($cacheKey, $currentCount + 1, $rateLimit['window']);
    }

    /**
     * Get rate limit configuration
     *
     * @return array
     */
    protected function getRateLimit(): array
    {
        $rateLimits = [
            'shopify' => ['limit' => 40, 'window' => 60], // 40 requests per minute
            'woocommerce' => ['limit' => 100, 'window' => 60], // 100 requests per minute
        ];

        return $rateLimits[$this->externalSystem] ?? ['limit' => 60, 'window' => 60];
    }

    /**
     * Get products from Shopify
     *
     * @param int $limit The number of products to retrieve
     * @param int $offset The offset for pagination
     * @return array
     */
    protected function getShopifyProducts(int $limit, int $offset): array
    {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'X-Shopify-Access-Token' => $this->config['access_token'],
                'Content-Type' => 'application/json',
            ])
            ->get("{$this->config['base_url']}/admin/api/{$this->config['version']}/products.json", [
                'limit' => $limit,
                'page' => ($offset / $limit) + 1,
            ]);

        if ($response->successful()) {
            return $response->json('products', []);
        }

        throw new Exception("Shopify API error: {$response->status()} - {$response->body()}");
    }

    /**
     * Get a specific product from Shopify
     *
     * @param string $externalId The external product ID
     * @return array|null
     */
    protected function getShopifyProduct(string $externalId): ?array
    {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'X-Shopify-Access-Token' => $this->config['access_token'],
                'Content-Type' => 'application/json',
            ])
            ->get("{$this->config['base_url']}/admin/api/{$this->config['version']}/products/{$externalId}.json");

        if ($response->successful()) {
            return $response->json('product');
        }

        return null;
    }

    /**
     * Create a product in Shopify
     *
     * @param array $productData The product data to create
     * @return array|null
     */
    protected function createShopifyProduct(array $productData): ?array
    {
        $shopifyProduct = [
            'product' => [
                'title' => $productData['name'],
                'body_html' => $productData['description'] ?? '',
                'vendor' => 'Sellora',
                'product_type' => $productData['category'] ?? 'General',
                'variants' => [
                    [
                        'price' => $productData['price'] ?? 0,
                        'sku' => $productData['sku'] ?? '',
                        'inventory_management' => 'shopify',
                        'inventory_quantity' => $productData['stock_quantity'] ?? 0,
                    ]
                ]
            ]
        ];

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'X-Shopify-Access-Token' => $this->config['access_token'],
                'Content-Type' => 'application/json',
            ])
            ->post("{$this->config['base_url']}/admin/api/{$this->config['version']}/products.json", $shopifyProduct);

        if ($response->successful()) {
            return $response->json('product');
        }

        return null;
    }

    /**
     * Get orders from Shopify
     *
     * @param int $limit The number of orders to retrieve
     * @param int $offset The offset for pagination
     * @return array
     */
    protected function getShopifyOrders(int $limit, int $offset): array
    {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'X-Shopify-Access-Token' => $this->config['access_token'],
                'Content-Type' => 'application/json',
            ])
            ->get("{$this->config['base_url']}/admin/api/{$this->config['version']}/orders.json", [
                'limit' => $limit,
                'page' => ($offset / $limit) + 1,
                'status' => 'any',
            ]);

        if ($response->successful()) {
            return $response->json('orders', []);
        }

        throw new Exception("Shopify API error: {$response->status()} - {$response->body()}");
    }

    /**
     * Get a specific order from Shopify
     *
     * @param string $externalId The external order ID
     * @return array|null
     */
    protected function getShopifyOrder(string $externalId): ?array
    {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'X-Shopify-Access-Token' => $this->config['access_token'],
                'Content-Type' => 'application/json',
            ])
            ->get("{$this->config['base_url']}/admin/api/{$this->config['version']}/orders/{$externalId}.json");

        if ($response->successful()) {
            return $response->json('order');
        }

        return null;
    }

    /**
     * Test Shopify connection
     *
     * @return bool
     */
    protected function testShopifyConnection(): bool
    {
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'X-Shopify-Access-Token' => $this->config['access_token'],
                'Content-Type' => 'application/json',
            ])
            ->get("{$this->config['base_url']}/admin/api/{$this->config['version']}/shop.json");

        return $response->successful();
    }

    /**
     * Get products from WooCommerce
     *
     * @param int $limit The number of products to retrieve
     * @param int $offset The offset for pagination
     * @return array
     */
    protected function getWooCommerceProducts(int $limit, int $offset): array
    {
        $response = Http::timeout($this->timeout)
            ->withBasicAuth($this->config['consumer_key'], $this->config['consumer_secret'])
            ->get("{$this->config['base_url']}/wp-json/{$this->config['version']}/products", [
                'per_page' => $limit,
                'page' => ($offset / $limit) + 1,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception("WooCommerce API error: {$response->status()} - {$response->body()}");
    }

    /**
     * Get a specific product from WooCommerce
     *
     * @param string $externalId The external product ID
     * @return array|null
     */
    protected function getWooCommerceProduct(string $externalId): ?array
    {
        $response = Http::timeout($this->timeout)
            ->withBasicAuth($this->config['consumer_key'], $this->config['consumer_secret'])
            ->get("{$this->config['base_url']}/wp-json/{$this->config['version']}/products/{$externalId}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Create a product in WooCommerce
     *
     * @param array $productData The product data to create
     * @return array|null
     */
    protected function createWooCommerceProduct(array $productData): ?array
    {
        $wooProduct = [
            'name' => $productData['name'],
            'description' => $productData['description'] ?? '',
            'short_description' => $productData['short_description'] ?? '',
            'sku' => $productData['sku'] ?? '',
            'regular_price' => (string)($productData['price'] ?? 0),
            'manage_stock' => true,
            'stock_quantity' => $productData['stock_quantity'] ?? 0,
            'status' => 'publish',
        ];

        $response = Http::timeout($this->timeout)
            ->withBasicAuth($this->config['consumer_key'], $this->config['consumer_secret'])
            ->post("{$this->config['base_url']}/wp-json/{$this->config['version']}/products", $wooProduct);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Get orders from WooCommerce
     *
     * @param int $limit The number of orders to retrieve
     * @param int $offset The offset for pagination
     * @return array
     */
    protected function getWooCommerceOrders(int $limit, int $offset): array
    {
        $response = Http::timeout($this->timeout)
            ->withBasicAuth($this->config['consumer_key'], $this->config['consumer_secret'])
            ->get("{$this->config['base_url']}/wp-json/{$this->config['version']}/orders", [
                'per_page' => $limit,
                'page' => ($offset / $limit) + 1,
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception("WooCommerce API error: {$response->status()} - {$response->body()}");
    }

    /**
     * Get a specific order from WooCommerce
     *
     * @param string $externalId The external order ID
     * @return array|null
     */
    protected function getWooCommerceOrder(string $externalId): ?array
    {
        $response = Http::timeout($this->timeout)
            ->withBasicAuth($this->config['consumer_key'], $this->config['consumer_secret'])
            ->get("{$this->config['base_url']}/wp-json/{$this->config['version']}/orders/{$externalId}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Test WooCommerce connection
     *
     * @return bool
     */
    protected function testWooCommerceConnection(): bool
    {
        $response = Http::timeout($this->timeout)
            ->withBasicAuth($this->config['consumer_key'], $this->config['consumer_secret'])
            ->get("{$this->config['base_url']}/wp-json/{$this->config['version']}/system_status");

        return $response->successful();
    }
}