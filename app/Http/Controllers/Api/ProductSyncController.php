<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductSyncService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProductSyncController extends Controller
{
    private $syncService;

    public function __construct(ProductSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Sync a specific product
     *
     * @param Request $request
     * @param int $productId
     * @return JsonResponse
     */
    public function syncProduct(Request $request, int $productId): JsonResponse
    {
        try {
            $product = Product::findOrFail($productId);
            
            $changes = $request->input('changes', []);
            $original = $request->input('original', []);
            
            $syncResults = $this->syncService->syncProductData($product, $changes, $original);
            
            return response()->json([
                'success' => true,
                'message' => 'Product synchronized successfully',
                'data' => $syncResults
            ]);
            
        } catch (\Exception $e) {
            Log::error('API Product sync failed', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Product synchronization failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync all products
     *
     * @return JsonResponse
     */
    public function syncAllProducts(): JsonResponse
    {
        try {
            $syncResults = $this->syncService->syncAllProducts();
            
            return response()->json([
                'success' => true,
                'message' => 'All products synchronized successfully',
                'data' => $syncResults
            ]);
            
        } catch (\Exception $e) {
            Log::error('API Bulk product sync failed', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Bulk product synchronization failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sync status for a product
     *
     * @param int $productId
     * @return JsonResponse
     */
    public function getSyncStatus(int $productId): JsonResponse
    {
        try {
            $product = Product::findOrFail($productId);
            $status = $this->syncService->getSyncStatus($product);
            
            return response()->json([
                'success' => true,
                'data' => $status
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get sync status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update product data with automatic sync
     *
     * @param Request $request
     * @param int $productId
     * @return JsonResponse
     */
    public function updateProductWithSync(Request $request, int $productId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|max:100|unique:products,sku,' . $productId,
            'stock' => 'sometimes|integer|min:0',
            'purchase_price' => 'sometimes|numeric|min:0',
            'selling_price' => 'sometimes|numeric|min:0',
            'expiration_date' => 'sometimes|date|nullable',
            'category_id' => 'sometimes|exists:categories,id',
            'brand_id' => 'sometimes|exists:brands,id',
            'unit_id' => 'sometimes|exists:units,id',
            'status' => 'sometimes|in:active,inactive',
            'min_stock_level' => 'sometimes|integer|min:0',
            'max_stock_level' => 'sometimes|integer|min:0',
            'barcode' => 'sometimes|string|max:255|nullable',
            'tax_rate' => 'sometimes|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product = Product::findOrFail($productId);
            $original = $product->toArray();
            
            // Update product
            $product->fill($request->only([
                'name', 'sku', 'stock', 'purchase_price', 'selling_price',
                'expiration_date', 'category_id', 'brand_id', 'unit_id', 'status',
                'min_stock_level', 'max_stock_level', 'barcode', 'tax_rate'
            ]));
            
            $changes = $product->getDirty();
            $product->save();
            
            // Trigger synchronization
            $syncResults = $this->syncService->syncProductData($product, $changes, $original);
            
            return response()->json([
                'success' => true,
                'message' => 'Product updated and synchronized successfully',
                'data' => [
                    'product' => $product->fresh()->load(['category', 'brand', 'unit']),
                    'sync_results' => $syncResults
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('API Product update with sync failed', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Product update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time product data
     *
     * @param int $productId
     * @return JsonResponse
     */
    public function getRealtimeData(int $productId): JsonResponse
    {
        try {
            $product = Product::with([
                'category', 'brand', 'unit', 'stockBalances.warehouse'
            ])->findOrFail($productId);
            
            $data = [
                'product' => $product,
                'stock_summary' => [
                    'total_on_hand' => $product->stockBalances->sum('qty_on_hand'),
                    'total_reserved' => $product->stockBalances->sum('qty_reserved'),
                    'available' => $product->stockBalances->sum('qty_on_hand') - $product->stockBalances->sum('qty_reserved')
                ],
                'sync_status' => $this->syncService->getSyncStatus($product),
                'last_updated' => $product->updated_at->toISOString()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get realtime data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch update multiple products
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function batchUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.data' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $results = [];
        $errors = [];

        foreach ($request->input('products') as $productData) {
            try {
                $product = Product::findOrFail($productData['id']);
                $original = $product->toArray();
                
                $product->fill($productData['data']);
                $changes = $product->getDirty();
                $product->save();
                
                $syncResults = $this->syncService->syncProductData($product, $changes, $original);
                
                $results[] = [
                    'product_id' => $product->id,
                    'success' => true,
                    'sync_results' => $syncResults
                ];
                
            } catch (\Exception $e) {
                $errors[] = [
                    'product_id' => $productData['id'],
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => empty($errors),
            'message' => empty($errors) ? 'All products updated successfully' : 'Some products failed to update',
            'data' => [
                'successful' => $results,
                'failed' => $errors,
                'summary' => [
                    'total' => count($request->input('products')),
                    'successful' => count($results),
                    'failed' => count($errors)
                ]
            ]
        ]);
    }
}