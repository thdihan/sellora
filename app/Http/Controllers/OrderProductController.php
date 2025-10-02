<?php

/**
 * Order Product Controller
 *
 * Handles product information requests for order creation.
 * This controller is accessible to all authenticated users for order creation purposes.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * OrderProductController
 * 
 * Handles product information requests for order creation.
 * This controller is accessible to all authenticated users for order creation purposes.
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class OrderProductController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Only require authentication, no role restrictions for order creation
        $this->middleware('auth');
    }

    /**
     * Get product information for order creation
     * 
     * @param int $id Product ID
     * 
     * @return JsonResponse
     */
    public function getProductInfo($id): JsonResponse
    {
        try {
            $product = Product::where('id', $id)
                ->where('status', true) // Only active products
                ->first();

            if (!$product) {
                return response()->json(
                    [
                        'error' => 'Product not found or inactive',
                        'available_stock' => 0,
                        'price' => 0
                    ],
                    404
                );
            }

            // Calculate available stock
            $availableStock = $product->available_stock ?? 0;
            
            return response()->json(
                [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'available_stock' => $availableStock,
                    'price' => $product->selling_price ?? 0,
                    'selling_price' => $product->selling_price ?? 0,
                    'unit' => $product->unit ? $product->unit->name : 'Unit',
                    'category' => $product->category ? $product->category->name : 'Uncategorized',
                    'status' => $product->status ? 'active' : 'inactive'
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'error' => 'Failed to fetch product information',
                    'available_stock' => 0,
                    'price' => 0
                ],
                500
            );
        }
    }

    /**
     * Get all active products for order creation
     * 
     * @param Request $request The HTTP request
     * 
     * @return JsonResponse
     */
    public function getActiveProducts(Request $request): JsonResponse
    {
        try {
            $query = Product::where('status', true)
                ->with(['unit', 'category']);

            // Add search functionality if search term provided
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(
                    function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    }
                );
            }

            $products = $query->select(
                [
                    'id', 'name', 'sku', 'selling_price', 
                    'category_id', 'unit_id', 'status'
                ]
            )->get();

            $formattedProducts = $products->map(
                function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'price' => $product->selling_price ?? 0,
                        'selling_price' => $product->selling_price ?? 0,
                        'available_stock' => $product->available_stock ?? 0,
                        'unit' => $product->unit ? $product->unit->name : 'Unit',
                        'category' => $product->category ? $product->category->name : 'Uncategorized'
                    ];
                }
            );

            return response()->json(
                [
                    'success' => true,
                    'products' => $formattedProducts,
                    'total' => $products->count()
                ]
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Failed to fetch products',
                    'products' => [],
                    'total' => 0
                ],
                500
            );
        }
    }
}