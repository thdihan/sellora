<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxHead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TaxHeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TaxHead::with('creator');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('kind', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('kind')) {
            $query->where('kind', $request->kind);
        }
        
        $taxHeads = $query->orderBy('created_at', 'desc')->paginate(15);
        
        if ($request->expectsJson()) {
            return response()->json($taxHeads);
        }
        
        return view('admin.tax-heads.index', compact('taxHeads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.tax-heads.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tax_heads,code',
            'kind' => 'required|in:VAT,AIT,OTHER',
            'percentage' => 'required|numeric|min:0|max:100',
            'visible_to_client' => 'boolean',
        ]);
        
        $validated['created_by'] = Auth::id();
        $validated['visible_to_client'] = $request->boolean('visible_to_client', true);
        
        $taxHead = TaxHead::create($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tax head created successfully.',
                'data' => $taxHead->load('creator')
            ], 201);
        }
        
        return redirect()->route('admin.tax-heads.index')
            ->with('success', 'Tax head created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TaxHead $taxHead)
    {
        $taxHead->load(['creator', 'orderTaxLines.order']);
        
        if (request()->expectsJson()) {
            return response()->json($taxHead);
        }
        
        return view('admin.tax-heads.show', compact('taxHead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaxHead $taxHead): View
    {
        return view('admin.tax-heads.edit', compact('taxHead'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaxHead $taxHead)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:tax_heads,code,' . $taxHead->id,
            'kind' => 'required|in:VAT,AIT,OTHER',
            'percentage' => 'required|numeric|min:0|max:100',
            'visible_to_client' => 'boolean',
        ]);
        
        $validated['visible_to_client'] = $request->boolean('visible_to_client', true);
        
        $taxHead->update($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tax head updated successfully.',
                'data' => $taxHead->load('creator')
            ]);
        }
        
        return redirect()->route('admin.tax-heads.index')
            ->with('success', 'Tax head updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaxHead $taxHead)
    {
        // Check if tax head is being used in any orders
        if ($taxHead->orderTaxLines()->exists()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete tax head that is being used in orders.'
                ], 422);
            }
            
            return redirect()->route('admin.tax-heads.index', request()->query())
                ->with('error', 'Cannot delete tax head that is being used in orders.');
        }
        
        $taxHead->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tax head deleted successfully.'
            ]);
        }
        
        return redirect()->route('admin.tax-heads.index', request()->query())
            ->with('success', 'Tax head deleted successfully.');
    }
}
