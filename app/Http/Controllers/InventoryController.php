<?php

/**
 * Inventory Controller
 *
 * Handles inventory management operations including stock balances,
 * product batches, stock transfers, and inventory transactions.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Rayhan Doe <rayhan@example.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockBalance;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * InventoryController handles inventory management operations
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items
     *
     * @param Request $request The HTTP request instance
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get the main warehouse
        $mainWarehouse = Warehouse::getMain();
        if (!$mainWarehouse) {
            throw new \Exception('Main warehouse not found. Please ensure at least one warehouse is marked as main.');
        }
        $defaultWarehouseId = $mainWarehouse->id;
        
        // Start with products and left join stock balances
        $query = Product::with(['category', 'unit', 'brand'])
            ->leftJoin(
                'stock_balances',
                function ($join) use ($defaultWarehouseId) {
                    $join->on('products.id', '=', 'stock_balances.product_id')
                        ->where('stock_balances.warehouse_id', '=', $defaultWarehouseId);
                }
            )
            ->leftJoin('warehouses', 'stock_balances.warehouse_id', '=', 'warehouses.id')
            ->select(
                'products.*',
                'stock_balances.qty_on_hand',
                'stock_balances.qty_reserved',
                'stock_balances.warehouse_id',
                'warehouses.name as warehouse_name'
            )
            ->where('products.status', true);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(
                function ($q) use ($search) {
                    $q->where('products.name', 'like', "%{$search}%")
                        ->orWhere('products.sku', 'like', "%{$search}%")
                        ->orWhere('products.barcode', 'like', "%{$search}%");
                }
            );
        }
        
        // Handle stock status filtering
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
            case 'in_stock':
                $query->where('stock_balances.qty_on_hand', '>', 0);
                break;
            case 'low_stock':
                $query->whereRaw('COALESCE(stock_balances.qty_on_hand, 0) <= COALESCE(products.min_stock_level, 10)')
                    ->whereRaw('COALESCE(stock_balances.qty_on_hand, 0) > 0');
                break;
            case 'out_of_stock':
                $query->whereRaw('COALESCE(stock_balances.qty_on_hand, 0) <= 0');
                break;
            }
        }
        
        // Legacy support for low_stock parameter
        if ($request->filled('low_stock')) {
            $query->whereRaw('COALESCE(stock_balances.qty_on_hand, 0) <= COALESCE(products.min_stock_level, 10)');
        }
        
        $products = $query->orderBy('products.updated_at', 'desc')->paginate(20);
        
        // Transform results to match expected structure
        $stockBalances = $products->getCollection()->map(
            function ($product) use ($defaultWarehouseId) {
                return (object) [
                    'id' => $product->id,
                    'product_id' => $product->id,
                    'warehouse_id' => $product->warehouse_id ?? $defaultWarehouseId,
                    'qty_on_hand' => $product->qty_on_hand ?? 0,
                    'qty_reserved' => $product->qty_reserved ?? 0,
                    'product' => $product,
                    'warehouse' => (object) ['name' => $product->warehouse_name ?? 'Main Warehouse'],
                    'batch' => null,
                    'updated_at' => $product->updated_at
                ];
            }
        );
        
        $products->setCollection($stockBalances);
        $stockBalances = $products;
        // Since we're using single warehouse, we only need the main warehouse
        $warehouses = collect([$mainWarehouse]);
        
        $totalProducts = Product::active()->count();
        $lowStockProducts = StockBalance::whereHas(
            'product',
            function ($q) {
                $q->whereRaw('qty_on_hand <= reorder_level');
            }
        )->distinct('product_id')->count();
        
        $outOfStockProducts = StockBalance::where('qty_on_hand', 0)
            ->distinct('product_id')->count();
        
        $totalStockValue = StockBalance::join('products', 'stock_balances.product_id', '=', 'products.id')
            ->selectRaw('SUM(qty_on_hand * purchase_price) as total')
            ->value('total') ?? 0;
        
        $stats = [
            'total_products' => $totalProducts,
            'active_products' => $totalProducts,
            'low_stock_count' => $lowStockProducts,
            'out_of_stock_count' => $outOfStockProducts,
            'total_stock_value' => $totalStockValue
        ];
        
        $recentTransactions = StockBalance::with(['product', 'warehouse'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        
        $lowStockProducts = StockBalance::with(['product', 'warehouse'])
            ->where('qty_on_hand', '<=', 10)
            ->where('qty_on_hand', '>', 0)
            ->limit(10)
            ->get();
        
        $expiringBatches = StockBalance::with(['product', 'warehouse'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>', now())
            ->limit(10)
            ->get();
        
        $chartData = [
            'labels' => $stockBalances->take(10)->pluck('product.name')->toArray(),
            'datasets' => [[
                'label' => 'Stock Quantity',
                'data' => $stockBalances->take(10)->pluck('qty_on_hand')->toArray(),
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'borderWidth' => 1
            ]]
        ];
        
        return view(
            'inventory.index',
            compact(
                'stockBalances', 
                'warehouses', 
                'stats',
                'recentTransactions',
                'lowStockProducts',
                'expiringBatches',
                'chartData'
            )
        );
    }
    
    /**
     * Display detailed inventory information for a specific product
     *
     * @param int $id The product ID
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $product = Product::with(['category', 'unit', 'brand'])->findOrFail($id);
        
        // Get stock balances for this product across all warehouses
        $stockBalances = StockBalance::with(['warehouse', 'batch'])
            ->where('product_id', $id)
            ->get();
        
        // Get recent transactions for this product
        $recentTransactions = StockTransaction::with(['warehouse', 'batch'])
            ->where('product_id', $id)
            ->latest()
            ->limit(10)
            ->get();
        
        // Calculate total stock across all warehouses
        $totalStock = $stockBalances->sum('qty_on_hand');
        $totalReserved = $stockBalances->sum('qty_reserved');
        $availableStock = $totalStock - $totalReserved;
        
        // Get main warehouse for reference
        $mainWarehouse = Warehouse::getMain();
        $warehouses = collect([$mainWarehouse]);
        
        return view(
            'inventory.show', 
            compact(
                'product', 
                'stockBalances', 
                'recentTransactions', 
                'totalStock', 
                'totalReserved', 
                'availableStock',
                'warehouses'
            )
        );
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit($id)
    {
        $stockBalance = StockBalance::with(['product', 'warehouse', 'batch'])->findOrFail($id);
        return view('inventory.edit', compact('stockBalance'));
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);

        $stockBalance = StockBalance::findOrFail($id);
        $stockBalance->update($request->only(['quantity']));

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Remove the specified inventory item from storage.
     */
    public function destroy($id)
    {
        $stockBalance = StockBalance::findOrFail($id);
        $stockBalance->delete();
        
        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted successfully.');
    }

    /**
     * Display a listing of product batches
     *
     * @param Request $request The HTTP request instance
     *
     * @return \Illuminate\View\View
     */
    public function batches(Request $request)
    {
        $query = ProductBatch::with(['product', 'stockBalances.warehouse']);
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(
                function ($q) use ($search) {
                    $q->where('batch_no', 'like', "%{$search}%")
                        ->orWhereHas(
                            'product',
                            function ($pq) use ($search) {
                                $pq->where('name', 'like', "%{$search}%")
                                    ->orWhere('sku', 'like', "%{$search}%");
                            }
                        );
                }
            );
        }
        
        if ($request->filled('expired')) {
            $query->where('exp_date', '<', now());
        }
        
        if ($request->filled('near_expiry')) {
            $query->where('exp_date', '>=', now())
                ->where('exp_date', '<=', now()->addDays(30));
        }
        
        $batches = $query->latest()->paginate(15);
        
        return view(
            'inventory.batches',
            compact('batches')
        );
    }
    
    /**
     * Display the stock adjustments form
     *
     * @return \Illuminate\View\View
     */
    public function adjustments()
    {
        $products = Product::active()->with(['unit', 'stockBalances.warehouse'])->get();
        $mainWarehouse = Warehouse::getMain();
        if (!$mainWarehouse) {
            throw new \Exception('Main warehouse not found.');
        }
        $warehouses = collect([$mainWarehouse]);
        
        return view(
            'inventory.adjustments',
            compact('products', 'warehouses')
        );
    }
    
    /**
     * Store stock adjustments
     *
     * @param Request $request The HTTP request instance
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAdjustment(Request $request)
    {
        $mainWarehouse = Warehouse::getMain();
        if (!$mainWarehouse) {
            throw new \Exception('Main warehouse not found');
        }
        
        $validated = $request->validate(
            [
                'adjustments' => 'required|array|min:1',
                'adjustments.*.product_id' => 'required|exists:products,id',
                'adjustments.*.batch_id' => 'nullable|exists:product_batches,id',
                'adjustments.*.qty' => 'required|integer|not_in:0',
                'adjustments.*.note' => 'nullable|string|max:255',
            ]
        );
        
        DB::transaction(
            function () use ($validated, $mainWarehouse) {
                foreach ($validated['adjustments'] as $adjustment) {
                    $type = $adjustment['qty'] > 0 
                        ? StockTransaction::TYPE_ADJUSTMENT_IN 
                        : StockTransaction::TYPE_ADJUSTMENT_OUT;
                    
                    StockTransaction::create(
                        [
                            'product_id' => $adjustment['product_id'],
                            'warehouse_id' => $mainWarehouse->id,
                            'batch_id' => $adjustment['batch_id'] ?? null,
                            'qty' => abs($adjustment['qty']),
                            'type' => $type,
                            'note' => $adjustment['note'] ?? 'Stock adjustment',
                        ]
                    );
                    
                        $this->_updateStockBalance(
                            $adjustment['product_id'],
                            $mainWarehouse->id,
                            $adjustment['batch_id'] ?? null,
                            $adjustment['qty']
                        );
                }
            }
        );
        
        return redirect()->route('inventory.adjustments')
            ->with('success', 'Stock adjustments processed successfully.');
    }
    
    /**
     * Display the stock transfers form
     * Note: Transfers are disabled in single warehouse mode
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function transfers()
    {
        return redirect()->route('inventory.index')
            ->with('info', 'Stock transfers are not available in single warehouse mode.');
    }
    
    /**
     * Store stock transfers
     *
     * @param Request $request The HTTP request instance
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTransfer(Request $request)
    {
        return redirect()->route('inventory.index')
            ->with('error', 'Stock transfers are not available in single warehouse mode.');
    }

    
    /**
     * Display stock transactions
     *
     * @param Request $request The HTTP request instance
     *
     * @return \Illuminate\View\View
     */
    public function transactions(Request $request)
    {
        $query = StockTransaction::with(['product', 'warehouse', 'batch'])
            ->latest();
        
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $transactions = $query->paginate(20);
        $products = Product::active()->get();
        $mainWarehouse = Warehouse::getMain();
        $warehouses = collect([$mainWarehouse]);
        
        return view(
            'inventory.transactions',
            compact('transactions', 'products', 'warehouses')
        );
    }
    
    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        return view('inventory.create');
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0',
            'batch_id' => 'nullable|exists:product_batches,id',
        ]);

        // Implementation would go here based on your business logic
        return redirect()->route('inventory.index')->with('success', 'Inventory item created successfully.');
    }
    
    /**
     * Update stock balance for a product
     *
     * @param int      $productId   Product ID
     * @param int      $warehouseId Warehouse ID
     * @param int|null $batchId     Batch ID
     * @param int      $qtyChange   Quantity change
     *
     * @return void
     */
    private function _updateStockBalance($productId, $warehouseId, $batchId, $qtyChange)
    {
        $stockBalance = StockBalance::firstOrCreate(
            [
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'batch_id' => $batchId,
            ],
            [
                'qty_on_hand' => 0,
                'qty_reserved' => 0,
            ]
        );
        
        $stockBalance->increment('qty_on_hand', $qtyChange);
        
        if ($stockBalance->qty_on_hand < 0) {
            $stockBalance->update(['qty_on_hand' => 0]);
        }
    }
    
    /**
     * Get available stock for a product
     *
     * @param int      $productId   Product ID
     * @param int      $warehouseId Warehouse ID
     * @param int|null $batchId     Batch ID
     *
     * @return int
     */
    private function _getAvailableStock($productId, $warehouseId, $batchId = null)
    {
        $stockBalance = StockBalance::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('batch_id', $batchId)
            ->first();
        
        return $stockBalance ? $stockBalance->available_qty : 0;
    }
}