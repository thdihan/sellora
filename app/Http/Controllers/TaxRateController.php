<?php

/**
 * Tax Rate Controller
 *
 * This controller handles CRUD operations for tax rates.
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Developer <developer@example.com>
 * @license  MIT
 * @link     https://example.com
 */

namespace App\Http\Controllers;

use App\Http\Requests\TaxRateRequest;
use App\Models\TaxCode;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Tax Rate Controller Class
 *
 * Handles tax rate management operations.
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Developer <developer@example.com>
 * @license  MIT
 * @link     https://example.com
 */
class TaxRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request The request object
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $taxCode = TaxCode::findOrFail($request->route('tax'));

        $taxRates = TaxRate::where('tax_code_id', $taxCode->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tax.rates.index', compact('taxCode', 'taxRates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request The request object
     *
     * @return View
     */
    public function create(Request $request): View
    {
        $taxCode = TaxCode::findOrFail(
            $request->route('tax')
        );

        return view('tax.rates.create', compact('taxCode'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TaxRateRequest $request The tax rate request object
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TaxRateRequest $request)
    {
        $taxCode = TaxCode::findOrFail($request->route('tax'));

        $taxRate = TaxRate::create([
            'tax_code_id' => $taxCode->id,
            'rate' => $request->rate,
            'effective_date' => $request->effective_date,
            'description' => $request->description,
        ]);

        return redirect()->route('tax.rates.show', [
            'tax' => $taxCode->id,
            'rate' => $taxRate->id
        ])->with('success', 'Tax rate created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param TaxRate $taxRate The tax rate model instance
     *
     * @return View
     */
    public function show(TaxRate $taxRate): View
    {
        return view('tax.rates.show', compact('taxRate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TaxRate $taxRate The tax rate model instance
     *
     * @return View
     */
    public function edit(TaxRate $taxRate): View
    {
        return view('tax.rates.edit', compact('taxRate'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TaxRateRequest $request The tax rate request object
     * @param TaxRate        $taxRate The tax rate model instance
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TaxRateRequest $request, TaxRate $taxRate)
    {
        $taxRate->update([
            'rate' => $request->rate,
            'effective_date' => $request->effective_date,
            'description' => $request->description,
        ]);

        return redirect()->route('tax.rates.show', [
            'tax' => $taxRate->tax_code_id,
            'rate' => $taxRate->id
        ])->with('success', 'Tax rate updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param TaxRate $taxRate The tax rate model instance
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TaxRate $taxRate)
    {
        // Get the tax code ID before deleting the tax rate
        $taxCodeId = $taxRate->tax_code_id;

        $taxRate->delete();

        // Check if there are any remaining tax rates for this tax code
        $remainingRates = TaxRate::where('tax_code_id', $taxCodeId)->count();

        if ($remainingRates > 0) {
            return redirect()->route('tax.rates.index', ['tax' => $taxCodeId] + request()->query())
                ->with('success', 'Tax rate deleted successfully.');
        } else {
            return redirect()->route('tax.rates.index', ['tax' => $taxCodeId] + request()->query())
                ->with('info', 'Tax rate deleted. No more rates exist for this tax code.');
        }
    }

    /**
     * Get tax rates as JSON for API calls.
     *
     * @return JsonResponse
     */
    public function api(): JsonResponse
    {
        $taxRates = TaxRate::with(['taxCode', 'taxRules'])
            ->active()
            ->effective()
            ->get();

        return response()->json($taxRates);
    }
}