<?php

/**
 * Tax Rule Controller
 *
 * Handles CRUD operations for tax rules including creation, editing,
 * deletion and display of tax rules with their associated tax rates.
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\TaxRule;
use App\Models\TaxRate;
use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class TaxRuleController
 *
 * Manages tax rule operations including listing, creating, updating and deleting tax rules.
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */
class TaxRuleController extends Controller
{
    /**
     * Display a listing of tax rules.
     *
     * @return View
     */
    public function index(): View
    {
        $taxRules = TaxRule::with(['taxRate.taxCode'])
            ->active()
            ->orderBy('priority')
            ->paginate(15);

        return view('tax.rules.index', compact('taxRules'));
    }

    /**
     * Show the form for creating a new tax rule.
     *
     * @return View
     */
    public function create(): View
    {
        $taxRates = TaxRate::with('taxCode')->active()->get();
        $categories = ProductCategory::active()->get();
        $products = Product::active()->get();
        
        return view('tax.rules.create', compact('taxRates', 'categories', 'products'));
    }

    /**
     * Store a newly created tax rule.
     *
     * @param  Request $request The HTTP request containing tax rule data
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'tax_rate_id' => 'required|exists:tax_rates,id',
                'applies_to' => 'required|in:all,category,product,shipping,fees',
                'category_id' => 'nullable|exists:product_categories,id',
                'product_id' => 'nullable|exists:products,id',
                'price_mode' => 'required|in:INCLUSIVE,EXCLUSIVE',
                'bearer' => 'required|in:CUSTOMER,COMPANY',
                'reverse_charge' => 'boolean',
                'zero_rated' => 'boolean',
                'exempt' => 'boolean',
                'withholding' => 'boolean',
                'withholding_percent' => 'nullable|numeric|min:0|max:100',
                'taxable_discounts' => 'required|in:NONE,BEFORE_TAX,AFTER_TAX',
                'taxable_shipping' => 'boolean',
                'place_of_supply' => 'required|in:ORIGIN,DESTINATION,AUTO',
                'rounding' => 'required|in:LINE,SUBTOTAL,INVOICE',
                'priority' => 'required|integer|min:0',
                'is_active' => 'boolean',
                'comments' => 'nullable|string',
            ]
        );

        $taxRule = TaxRule::create($validated);

        return redirect()->route('rules.edit', $taxRule)
            ->with('success', 'Tax rule created successfully.');
    }

    /**
     * Display the specified tax rule.
     *
     * @param  TaxRule $taxRule The tax rule to display
     * @return View
     */
    public function show(TaxRule $taxRule): View
    {
        $taxRule->load(['taxRate.taxCode', 'category', 'product']);
        return view('tax.rules.show', compact('taxRule'));
    }

    /**
     * Show the form for editing the specified tax rule.
     *
     * @param  TaxRule $taxRule The tax rule to edit
     * @return View
     */
    public function edit(TaxRule $taxRule): View
    {
        $taxRates = TaxRate::with('taxCode')->active()->get();
        $categories = ProductCategory::active()->get();
        $products = Product::active()->get();
        
        return view('tax.rules.edit', compact('taxRule', 'taxRates', 'categories', 'products'));
    }

    /**
     * Update the specified tax rule.
     *
     * @param  Request $request The HTTP request containing updated tax rule data
     * @param  TaxRule $taxRule The tax rule to update
     * @return RedirectResponse
     */
    public function update(Request $request, TaxRule $taxRule): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'tax_rate_id' => 'required|exists:tax_rates,id',
                'applies_to' => 'required|in:all,category,product,shipping,fees',
                'category_id' => 'nullable|exists:product_categories,id',
                'product_id' => 'nullable|exists:products,id',
                'price_mode' => 'required|in:INCLUSIVE,EXCLUSIVE',
                'bearer' => 'required|in:CUSTOMER,COMPANY',
                'reverse_charge' => 'boolean',
                'zero_rated' => 'boolean',
                'exempt' => 'boolean',
                'withholding' => 'boolean',
                'withholding_percent' => 'nullable|numeric|min:0|max:100',
                'taxable_discounts' => 'required|in:NONE,BEFORE_TAX,AFTER_TAX',
                'taxable_shipping' => 'boolean',
                'place_of_supply' => 'required|in:ORIGIN,DESTINATION,AUTO',
                'rounding' => 'required|in:LINE,SUBTOTAL,INVOICE',
                'priority' => 'required|integer|min:0',
                'is_active' => 'boolean',
                'comments' => 'nullable|string',
            ]
        );

        $taxRule->update($validated);

        return redirect()->route('rules.show', $taxRule)
            ->with('success', 'Tax rule updated successfully.');
    }

    /**
     * Remove the specified tax rule.
     *
     * @param  TaxRule $taxRule The tax rule to delete
     * @return RedirectResponse
     */
    public function destroy(TaxRule $taxRule): RedirectResponse
    {
        $taxRule->delete();

        return redirect()->route('tax.rules.index', request()->query())
            ->with('success', 'Tax rule deleted successfully.');
    }

    /**
     * Get tax rules as JSON for API calls.
     *
     * @return JsonResponse
     */
    public function api(): JsonResponse
    {
        $taxRules = TaxRule::with(['taxRate.taxCode', 'category', 'product'])
            ->active()
            ->orderBy('priority')
            ->get();

        return response()->json($taxRules);
    }
}