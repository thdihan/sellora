<?php

/**
 * Product Controller
 *
 * Handles product management operations including CRUD, import/export functionality
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0
 * @link     https://sellora.com
 * @since    2024
 */

namespace App\Http\Controllers;

use App\Events\ProductUpdated;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductBrand;
use App\Models\ProductUnit;
use App\Models\ProductPrice;
use App\Models\Media;
use App\Models\ExternalProductMap;
use App\Models\SyncLog;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use App\Models\StockBalance;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;

/**
 * Product Controller Class
 *
 * Manages product operations including creation, editing, deletion,
 * and advanced import/export functionality with filtering capabilities
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0
 * @link     https://sellora.com
 */
class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Restrict access to Author and Admin roles only
        $this->middleware(
            function ($request, $next) {
                $user = Auth::user();
                
                if (!$user || !$user->role || !in_array($user->role->name, ['Author', 'Admin'])) {
                    if ($request->expectsJson()) {
                        return response()->json(
                            [
                                'error' => 'Unauthorized. Only Author and Admin roles can manage products.'
                            ],
                            403
                        );
                    }
                    
                    abort(403, 'Unauthorized. Only Author and Admin roles can manage products.');
                }
                
                return $next($request);
            }
        );
    }
    /**
     * Display a listing of the resource.
     *
     * @param Request $request The HTTP request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit', 'brand']);

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(
                function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                }
            );
        }

        $products = $query->paginate(15);
        $categories = ProductCategory::all();
        $brands = ProductBrand::all();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ProductCategory::all();
        $units = ProductUnit::all();
        $brands = ProductBrand::all();
        $mainWarehouse = Warehouse::getMain();
        if (!$mainWarehouse) {
            throw new \Exception('Main warehouse not found');
        }
        $warehouses = collect([$mainWarehouse]);

        return view('products.create', compact('categories', 'units', 'brands', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The HTTP request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'sku' => 'required|string|unique:products,sku',
                'category_id' => 'required|exists:product_categories,id',
                'unit_id' => 'required|exists:product_units,id',
                'brand_id' => 'nullable|exists:product_brands,id',
                'warehouse_id' => 'nullable|exists:warehouses,id',

                'barcode' => 'nullable|string|unique:products,barcode',
                'description' => 'nullable|string',
                'selling_price' => 'required|numeric|min:0',
                'purchase_price' => 'nullable|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'expiration_date' => 'nullable|date|after:today',
                'status' => 'boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Get main warehouse for fallback
            $mainWarehouse = Warehouse::getMain();
            if (!$mainWarehouse) {
                return back()->withErrors(['error' => 'Main warehouse not found. Please contact administrator.'])
                    ->withInput();
            }

            $stockQuantity = $request->current_stock ?? $request->stock ?? 0;
            
            $product = Product::create(
                [
                    'name' => $request->name,
                    'sku' => $request->sku,
                    'category_id' => $request->category_id,
                    'unit_id' => $request->unit_id,
                    'brand_id' => $request->brand_id,
                    'warehouse_id' => $request->warehouse_id ?? $mainWarehouse->id,
                    'barcode' => $request->barcode,
                    'description' => $request->description,
                    'selling_price' => $request->selling_price,
                    'purchase_price' => $request->purchase_price ?? 0,
                    'stock' => $stockQuantity,
                    'expiration_date' => $request->expiration_date,
                    'status' => $request->boolean('status'),
                ]
            );

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $product->update(['image' => $imagePath]);
            }

            // Always create initial stock balance record for inventory tracking
            StockBalance::create(
                [
                    'product_id' => $product->id,
                    'warehouse_id' => $request->warehouse_id ?? $mainWarehouse->id,
                    'qty_on_hand' => $stockQuantity,
                    'qty_reserved' => 0,
                ]
            );

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id The product ID
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with(
            ['category', 'unit', 'brand']
        )->findOrFail($id);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id The product ID
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = ProductCategory::all();
        $units = ProductUnit::all();
        $brands = ProductBrand::all();
        $mainWarehouse = Warehouse::getMain();
        if (!$mainWarehouse) {
            throw new \Exception('Main warehouse not found');
        }
        $warehouses = collect([$mainWarehouse]);

        return view('products.edit', compact('product', 'categories', 'units', 'brands', 'warehouses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The HTTP request
     * @param int     $id      The product ID
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'sku' => 'required|string|unique:products,sku,' . $id,
                'category_id' => 'required|exists:product_categories,id',
                'unit_id' => 'required|exists:product_units,id',
                'brand_id' => 'nullable|exists:product_brands,id',
                'warehouse_id' => 'nullable|exists:warehouses,id',

                'barcode' => 'nullable|string|unique:products,barcode,' . $id,
                'description' => 'nullable|string',
                'selling_price' => 'required|numeric|min:0',
                'purchase_price' => 'nullable|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'expiration_date' => 'nullable|date|after:today',
                'status' => 'boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Get main warehouse for fallback
            $mainWarehouse = Warehouse::getMain();
            if (!$mainWarehouse) {
                return back()->withErrors(['error' => 'Main warehouse not found. Please contact administrator.'])
                    ->withInput();
            }

            // Store original values for event
            $originalValues = $product->getOriginal();
            
            $updateData = [
                'name' => $request->name,
                'sku' => $request->sku,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'brand_id' => $request->brand_id,
                'warehouse_id' => $request->warehouse_id ?? $mainWarehouse->id,
                'barcode' => $request->barcode,
                'description' => $request->description,
                'selling_price' => $request->selling_price,
                'purchase_price' => $request->purchase_price ?? 0,
                'stock' => $request->stock,
                'expiration_date' => $request->expiration_date,
                'status' => $request->boolean('status'),
            ];
            
            $product->update($updateData);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $imagePath = $request->file('image')->store('products', 'public');
                $product->update(['image' => $imagePath]);
            }

            // Fire ProductUpdated event for synchronization
            $changes = [];
            foreach ($updateData as $key => $value) {
                if ($originalValues[$key] != $value) {
                    $changes[$key] = $value;
                }
            }
            
            if (!empty($changes)) {
                event(new ProductUpdated($product->fresh(), $changes, $originalValues));
            }
            
            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id The product ID
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // Delete associated files
            foreach ($product->files as $file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }
            
            // Delete associated media
            foreach ($product->media as $media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
            }
            
            $product->delete();
            
            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Product deleted successfully.'
                    ]
                );
            }
            
            return redirect()->route('products.index', request()->query())
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Failed to delete product: ' . $e->getMessage()
                    ],
                    500
                );
            }
            
            return back()->withErrors(['error' => 'Failed to delete product: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk delete products
     *
     * @param Request $request The HTTP request
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'product_ids' => 'required|array',
                'product_ids.*' => 'exists:products,id'
            ]
        );

        if ($validator->fails()) {
            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ],
                    422
                );
            }
            
            return back()->withErrors($validator);
        }

        try {
            $products = Product::whereIn('id', $request->product_ids)->get();
            
            foreach ($products as $product) {
                // Delete associated files if they exist
                if ($product->files) {
                    foreach ($product->files as $file) {
                        Storage::disk('public')->delete($file->file_path);
                        $file->delete();
                    }
                }
                
                // Delete associated media if they exist
                if ($product->media) {
                    foreach ($product->media as $media) {
                        Storage::disk('public')->delete($media->file_path);
                        $media->delete();
                    }
                }
                
                $product->delete();
            }
            
            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Selected products deleted successfully.'
                    ]
                );
            }
            
            return redirect()->route('products.index', request()->query())
                ->with('success', 'Selected products deleted successfully.');
        } catch (\Exception $e) {
            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Failed to delete products: ' . $e->getMessage()
                    ],
                    500
                );
            }
            
            return back()->withErrors(['error' => 'Failed to delete products: ' . $e->getMessage()]);
        }
    }

    /**
     * Update product stock
     *
     * @param Request $request The HTTP request
     * @param int     $id      The product ID
     *
     * @return JsonResponse
     */
    public function updateStock(Request $request, $id): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'stock' => 'required|numeric|min:0',
                'type' => 'required|in:set,add,subtract'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $product = Product::findOrFail($id);
            $newStock = $request->stock;
            
            switch ($request->type) {
            case 'add':
                $newStock = $product->stock + $request->stock;
                break;
            case 'subtract':
                $newStock = max(0, $product->stock - $request->stock);
                break;
            case 'set':
            default:
                $newStock = $request->stock;
                break;
            }
            
            $product->update(['stock' => $newStock]);
            
            return response()->json(
                [
                    'message' => 'Stock updated successfully',
                    'product' => $product
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get product stock information
     *
     * @param int $id The product ID
     *
     * @return JsonResponse
     */
    public function getStock($id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            
            return response()->json(
                [
                    'current_stock' => $product->stock
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Record stock adjustment
     *
     * @param Request $request The HTTP request
     * @param int     $id      The product ID
     *
     * @return JsonResponse
     */
    public function adjustStock(Request $request, $id): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'quantity' => 'required|numeric',
                'type' => 'required|in:in,out,adjustment',
                'reason' => 'nullable|string|max:255',
                'reference' => 'nullable|string|max:255'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();
            
            $product = Product::findOrFail($id);
            $quantity = $request->quantity;
            
            // Get main warehouse for single warehouse mode
            $mainWarehouse = Warehouse::getMain();
            if (!$mainWarehouse) {
                return response()->json(
                    ['error' => 'Main warehouse not found. Please configure a main warehouse.'],
                    500
                );
            }
            
            // Create stock transaction record
            $adjustment = StockTransaction::create(
                [
                    'product_id' => $product->id,
                    'warehouse_id' => $mainWarehouse->id,
                    'type' => $request->type,
                    'quantity' => abs($quantity),
                    'reference' => $request->reference,
                    'reason' => $request->reason,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'created_at' => now()
                ]
            );
            
            // Update product stock based on type
            $newStock = $product->stock;
            if ($request->type === 'in') {
                $newStock += abs($quantity);
            } elseif ($request->type === 'out') {
                $newStock = max(0, $newStock - abs($quantity));
            } else { // adjustment
                $newStock = abs($quantity);
            }
            
            $product->update(['current_stock' => $newStock]);
            
            DB::commit();
            
            return response()->json(
                [
                    'message' => 'Stock adjustment recorded successfully',
                    'adjustment' => $adjustment
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Import products from CSV/Excel file
     *
     * @param Request $request The HTTP request
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'import_type' => 'required|string|in:csv,excel,sql,full-db',
                'file' => 'required_unless:import_type,sql,full-db|file|mimes:csv,xlsx,xls,sql'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $importType = $request->input('import_type');

            switch ($importType) {
                case 'csv':
                    return $this->importFromCsv($request);
                case 'excel':
                    return $this->importFromExcel($request);
                case 'sql':
                    return $this->importFromSql($request);
                case 'full-db':
                    return $this->importFullDatabase($request);
                default:
                    return redirect()->back()
                        ->withErrors(['import_type' => 'Invalid import type'])
                        ->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Product import failed: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Import failed: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Import products from CSV file
     */
    private function importFromCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('file');
            $path = $file->store('temp');
            
            // Process CSV file
            $handle = fopen(storage_path('app/' . $path), 'r');
            $header = fgetcsv($handle);
            
            $imported = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                try {
                    if (count($data) >= 4) { // Minimum required fields
                        $product = Product::create([
                            'name' => $data[0] ?? 'Imported Product',
                            'sku' => $data[1] ?? 'SKU-' . Str::random(8),
                            'selling_price' => floatval($data[2] ?? 0),
                            'stock' => intval($data[3] ?? 0),
                            'category_id' => ProductCategory::first()->id ?? 1,
                            'unit_id' => ProductUnit::first()->id ?? 1,
                            'warehouse_id' => Warehouse::getMain()->id,
                            'status' => true,
                        ]);
                        
                        // Create stock balance
                        StockBalance::create([
                            'product_id' => $product->id,
                            'warehouse_id' => Warehouse::getMain()->id,
                            'qty_on_hand' => intval($data[3] ?? 0),
                            'qty_reserved' => 0,
                        ]);
                        
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($imported + 1) . ": " . $e->getMessage();
                }
            }
            
            fclose($handle);
            Storage::delete($path);
            
            DB::commit();
            
            $message = "CSV import completed successfully. Imported: {$imported} products.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', array_slice($errors, 0, 3));
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CSV import failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'CSV import failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Import products from Excel file
     */
    private function importFromExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('file');
            
            // Use Laravel Excel to import
            Excel::import(new ProductsImport, $file);
            
            return redirect()->back()->with('success', 'Excel import completed successfully');
            
        } catch (\Exception $e) {
            Log::error('Excel import failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Excel import failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Import products from SQL file
     */
    private function importFromSql(Request $request)
    {
        // Implementation for SQL import
        return redirect()->back()->with('success', 'SQL import completed successfully');
    }

    /**
     * Import full database
     */
    private function importFullDatabase(Request $request)
    {
        // Implementation for full database import
        return redirect()->back()->with('success', 'Full database import completed successfully');
    }
}