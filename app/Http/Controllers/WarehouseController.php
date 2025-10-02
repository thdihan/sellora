<?php

/**
 * Warehouse Controller
 * 
 * Handles warehouse management operations for single warehouse mode
 */

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class WarehouseController
 * 
 * Manages warehouse operations in single warehouse mode
 */
class WarehouseController extends Controller
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
                                'error' => 'Unauthorized. Only Author and Admin roles can manage warehouses.'
                            ],
                            403
                        );
                    }
                    
                    abort(403, 'Unauthorized. Only Author and Admin roles can manage warehouses.');
                }
                
                return $next($request);
            }
        );
    }

    /**
     * Display the main warehouse in single warehouse mode
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // In single warehouse mode, only show the main warehouse
        $mainWarehouse = Warehouse::getMain();
        if (!$mainWarehouse) {
            return redirect()->back()->with('error', 'Main warehouse not found. Please contact administrator.');
        }
        
        // Create a collection with only the main warehouse for consistency with the view
        $warehouses = collect([$mainWarehouse]);
        
        // Convert to paginator for view compatibility
        $warehouses = new \Illuminate\Pagination\LengthAwarePaginator(
            $warehouses,
            1,
            15,
            1,
            ['path' => request()->url()]
        );
        
        return view('warehouses.index', compact('warehouses'));
    }
    
    /**
     * Show the form for creating a new warehouse (disabled in single warehouse mode)
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        return redirect()->route('warehouses.index')
            ->with('error', 'Cannot create new warehouses in single warehouse mode.');
    }
    
    /**
     * Store a newly created warehouse (disabled in single warehouse mode)
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return redirect()->route('warehouses.index')
            ->with('error', 'Cannot create new warehouses in single warehouse mode.');
    }
    
    /**
     * Display the specified warehouse
     * 
     * @param Warehouse $warehouse
     * @return \Illuminate\View\View
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['stockBalances.product', 'stockTransactions.product']);
        
        $totalProducts = $warehouse->stockBalances()->distinct('product_id')->count();
        $totalStock = $warehouse->stockBalances()->sum('qty_on_hand');
        $recentTransactions = $warehouse->stockTransactions()
            ->with('product')
            ->latest()
            ->limit(10)
            ->get();
        
        return view(
            'warehouses.show',
            compact(
                'warehouse',
                'totalProducts',
                'totalStock',
                'recentTransactions'
            )
        );
    }
    
    /**
     * Show the form for editing the specified warehouse
     * 
     * @param Warehouse $warehouse
     * @return \Illuminate\View\View
     */
    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }
    
    /**
     * Update the specified warehouse in storage
     * 
     * @param Request $request
     * @param Warehouse $warehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
                'address' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'status' => 'boolean',
            ]
        );
        
        $warehouse->update($validated);
        
        return redirect()->route('warehouses.index')
            ->with('success', 'Warehouse updated successfully.');
    }
    
    /**
     * Remove the specified warehouse from storage (disabled in single warehouse mode)
     * 
     * @param Warehouse $warehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Warehouse $warehouse)
    {
        return redirect()->route('warehouses.index')
            ->with('error', 'Cannot delete warehouses in single warehouse mode.');
    }
}