<?php

/**
 * Tax Controller
 *
 * Handles tax code, tax rate, and tax rule management operations.
 * Provides CRUD operations for tax configurations and tax calculations.
 *
 * @package App\Http\Controllers
 * @author  Sellora Team
 * @version 1.0.0
 */

namespace App\Http\Controllers;

use App\Models\TaxCode;
use App\Models\TaxRate;
use App\Models\TaxRule;
use App\Services\TaxCalculationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Tax Controller
 *
 * Manages tax codes, rates, and rules for the application.
 * Provides comprehensive tax management functionality including
 * CRUD operations and tax calculations.
 */
class TaxController extends Controller
{
    protected $taxCalculationService;

    /**
     * Constructor
     *
     * @param TaxCalculationService $taxCalculationService Tax calculation service
     */
    public function __construct(TaxCalculationService $taxCalculationService)
    {
        $this->taxCalculationService = $taxCalculationService;
    }

    /**
     * Display a listing of tax codes.
     *
     * @return View
     */
    public function index(): View
    {
        $taxCodes = TaxCode::with(
            ['taxRates' => function ($query) {
                $query->active()->effective();
            }]
        )->paginate(15);

        return view('tax.index', compact('taxCodes'));
    }

    /**
     * Show the form for creating a new tax code.
     *
     * @return View
     */
    public function create(): View
    {
        return view('tax.create');
    }

    /**
     * Store a newly created tax code.
     *
     * @param Request $request The HTTP request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:20|unique:tax_codes',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]
        );

        $taxCode = TaxCode::create($validated);

        return redirect()->route('tax.show', $taxCode)
            ->with('success', 'Tax code created successfully.');
    }

    /**
     * Display the specified tax code.
     *
     * @param TaxCode $tax The tax code to display
     * @return View
     */
    public function show(TaxCode $tax): View
    {
        $tax->load(['taxRates.taxRules']);
        
        return view('tax.show', ['taxCode' => $tax]);
    }

    /**
     * Show the form for editing the specified tax code.
     *
     * @param TaxCode $tax The tax code to edit
     * @return View
     */
    public function edit(TaxCode $tax): View
    {
        return view('tax.edit', ['taxCode' => $tax]);
    }

    /**
     * Update the specified tax code.
     *
     * @param Request $request The HTTP request
     * @param TaxCode $tax The tax code to update
     * @return RedirectResponse
     */
    public function update(Request $request, TaxCode $tax): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:20|unique:tax_codes,code,' . $tax->id,
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]
        );

        $tax->update($validated);

        return redirect()->route('tax.show', $tax)
            ->with('success', 'Tax code updated successfully.');
    }

    /**
     * Remove the specified tax code.
     *
     * @param TaxCode $tax The tax code to delete
     * @return RedirectResponse
     */
    public function destroy(TaxCode $tax): RedirectResponse
    {
        if ($tax->taxRates()->exists()) {
            return redirect()->route('tax.index', request()->query())
                ->with('error', 'Cannot delete tax code with associated tax rates.');
        }

        $tax->delete();

        return redirect()->route('tax.index', request()->query())
            ->with('success', 'Tax code deleted successfully.');
    }

    /**
     * Store a new tax rate for a tax code.
     *
     * @param Request $request The HTTP request
     * @param TaxCode $tax The tax code to add rate to
     * @return RedirectResponse
     */
    public function storeRate(Request $request, TaxCode $tax): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'percent' => 'required|numeric|min:0|max:100',
            'country' => 'nullable|string|size:2',
            'region' => 'nullable|string|max:100',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['tax_code_id'] = $tax->id;

        // If this is set as default, unset other defaults for this tax code
        if (isset($validated['is_default']) && $validated['is_default']) {
            TaxRate::where('tax_code_id', $tax->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $taxRate = TaxRate::create($validated);

        return redirect()->route('tax.show', $tax)
            ->with('success', 'Tax rate added successfully.');
    }

    /**
     * Store a new tax rule for a tax rate.
     *
     * @param Request $request The HTTP request
     * @param TaxRate $taxRate The tax rate to add rule to
     * @return RedirectResponse
     */
    public function storeRule(Request $request, TaxRate $taxRate): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
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
        ]);

        $validated['tax_rate_id'] = $taxRate->id;
        TaxRule::create($validated);

        return redirect()->route('tax.show', $taxRate->taxCode)
            ->with('success', 'Tax rule added successfully.');
    }

    /**
     * Calculate tax for testing purposes.
     *
     * @param Request $request The HTTP request
     * @return JsonResponse
     */
    public function calculateTax(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'amount' => 'required|numeric|min:0',
            'country' => 'nullable|string|size:2',
            'region' => 'nullable|string|max:100',
        ]);

        $product = \App\Models\Product::findOrFail($validated['product_id']);
        $amount = $validated['amount'];
        $options = [
            'country' => $validated['country'] ?? null,
            'region' => $validated['region'] ?? null,
        ];

        $result = $this->taxCalculationService->calculateProductTax($product, $amount, $options);
        $validation = $this->taxCalculationService->validateTaxConfiguration($product);

        return response()->json([
            'tax_calculation' => $result,
            'validation' => $validation,
        ]);
    }

    /**
     * Get tax rates for a specific tax code.
     *
     * @param TaxCode $tax The tax code
     * @return JsonResponse
     */
    public function getRates(TaxCode $tax): JsonResponse
    {
        $rates = $tax->activeTaxRates()->with('taxRules')->get();
        
        return response()->json($rates);
    }

    /**
     * Get applicable tax rules for a product.
     *
     * @param Request $request The HTTP request
     * @return JsonResponse
     */
    public function getProductRules(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = \App\Models\Product::findOrFail($validated['product_id']);
        $validation = $this->taxCalculationService->validateTaxConfiguration($product);

        return response()->json($validation);
    }
}
