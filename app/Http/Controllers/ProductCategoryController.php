<?php

/**
 * Product Category Controller
 *
 * Handles product category management operations including CRUD functionality
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Product Category Controller Class
 *
 * Manages product category operations including creation, editing, deletion,
 * and listing of product categories
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class ProductCategoryController extends Controller
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
                                'error' => 'Unauthorized. Only Author and Admin roles can manage product categories.'
                            ],
                            403
                        );
                    }
                    
                    abort(403, 'Unauthorized. Only Author and Admin roles can manage product categories.');
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
        $categories = ProductCategory::latest()->paginate(15);
        return view('product-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('product-categories.create');
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
                'name' => 'required|string|max:255|unique:product_categories,name',
                'description' => 'nullable|string',
                'status' => 'boolean',
            ]
        );

        ProductCategory::create($validated);

        return redirect()->route('product-categories.index')
            ->with('success', 'Product category created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param ProductCategory $productCategory The product category model
     *
     * @return View
     */
    public function show(ProductCategory $productCategory): View
    {
        $productCategory->load('products');
        return view('product-categories.show', compact('productCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProductCategory $productCategory The product category model
     *
     * @return View
     */
    public function edit(ProductCategory $productCategory): View
    {
        return view('product-categories.edit', compact('productCategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request         $request         The HTTP request
     * @param ProductCategory $productCategory The product category model
     *
     * @return RedirectResponse
     */
    public function update(Request $request, ProductCategory $productCategory): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:255|unique:product_categories,name,' . $productCategory->id,
                'description' => 'nullable|string',
                'status' => 'boolean',
            ]
        );

        $productCategory->update($validated);

        return redirect()->route('product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProductCategory $productCategory The product category model
     *
     * @return RedirectResponse
     */
    public function destroy(ProductCategory $productCategory): RedirectResponse
    {
        // Check if category has products
        if ($productCategory->products()->count() > 0) {
            return redirect()->route('product-categories.index', request()->query())
                ->with('error', 'Cannot delete category that has products assigned to it.');
        }

        $productCategory->delete();

        return redirect()->route('product-categories.index', request()->query())
            ->with('success', 'Product category deleted successfully.');
    }
}