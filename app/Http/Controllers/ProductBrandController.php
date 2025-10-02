<?php

/**
 * Product Brand Controller
 *
 * Handles product brand management operations including CRUD functionality
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\ProductBrand;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Product Brand Controller Class
 *
 * Manages product brand operations including creation, editing, deletion,
 * and listing of product brands
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class ProductBrandController extends Controller
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
                                'error' => 'Unauthorized. Only Author and Admin roles can manage product brands.'
                            ],
                            403
                        );
                    }
                    
                    abort(403, 'Unauthorized. Only Author and Admin roles can manage product brands.');
                }
                
                return $next($request);
            }
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $brands = ProductBrand::latest()->paginate(15);
        return view('product-brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('product-brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The HTTP request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255|unique:product_brands,name',
                'description' => 'nullable|string',
                'status' => 'boolean',
            ]
        );

        ProductBrand::create($validated);

        return redirect()->route('product-brands.index')
            ->with('success', 'Product brand created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param ProductBrand $productBrand The product brand model
     *
     * @return View
     */
    public function show(ProductBrand $productBrand): View
    {
        $productBrand->load('products');
        return view('product-brands.show', compact('productBrand'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProductBrand $productBrand The product brand model
     *
     * @return View
     */
    public function edit(ProductBrand $productBrand): View
    {
        return view('product-brands.edit', compact('productBrand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request      $request      The HTTP request
     * @param ProductBrand $productBrand The product brand model
     *
     * @return RedirectResponse
     */
    public function update(Request $request, ProductBrand $productBrand): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255|unique:product_brands,name,' . $productBrand->id,
                'description' => 'nullable|string',
                'status' => 'boolean',
            ]
        );

        $productBrand->update($validated);

        return redirect()->route('product-brands.index')
            ->with('success', 'Product brand updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductBrand $productBrand The product brand model
     *
     * @return RedirectResponse
     */
    public function destroy(ProductBrand $productBrand): RedirectResponse
    {
        // Check if brand has products
        if ($productBrand->products()->count() > 0) {
            return redirect()->route('product-brands.index', request()->query())
                ->with('error', 'Cannot delete brand that has products assigned to it.');
        }

        $productBrand->delete();

        return redirect()->route('product-brands.index', request()->query())
            ->with('success', 'Product brand deleted successfully.');
    }
}