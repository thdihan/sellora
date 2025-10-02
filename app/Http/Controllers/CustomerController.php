<?php

/**
 * Customer Controller
 *
 * PHP version 8
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com> <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\DueCalculationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Customer Controller Class
 *
 * Handles all customer-related operations including CRUD operations,
 * analytics, and customer management functionality.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team
 * @license  MIT License
 * @link     https://sellora.com
 */
class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     *
     * @param Request $request The HTTP request object
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Customer::query();
        
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }
        
        $customers = $query->latest()->paginate(15);
        
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     *
     * @return View
     */
    public function create(): View
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage
     *
     * @param Request $request The HTTP request object
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'nullable|string|max:255',
            'full_address' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer
     *
     * @param Customer $customer The customer model instance
     *
     * @return View
     */
    public function show(Customer $customer): View
    {
        $customer->load('orders');
        $lastFiveOrders = $customer->getLastFiveOrders();
        $outstandingDue = $customer->outstanding_due;
        
        return view('customers.show', compact('customer', 'lastFiveOrders', 'outstandingDue'));
    }

    /**
     * Show the form for editing the specified customer
     *
     * @param Customer $customer The customer model instance
     *
     * @return View
     */
    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage
     *
     * @param  Request   $request  The HTTP request object
     * @param  Customer  $customer The customer model instance
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'nullable|string|max:255',
            'full_address' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage
     *
     * @param Customer $customer The customer model instance
     *
     * @return RedirectResponse
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index', request()->query())
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * API endpoint for customer search (typeahead)
     *
     * @param Request $request The HTTP request object
     *
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 3) {
            return response()->json([]);
        }
        
        $customers = Customer::search($query)
            ->select(
                'id', 'name', 'shop_name', 'phone', 'email'
            )
            ->limit(10)
            ->get()
            ->map(
                function ($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'shop_name' => $customer->shop_name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'display_name' => $customer->shop_name 
                        ? "{$customer->name} ({$customer->shop_name})"
                        : $customer->name,
                ];
            }
            );
        
        return response()->json($customers);
    }

    /**
     * API endpoint to get customer summary
     *
     * @param  Customer              $customer   The customer model instance
     * @param  DueCalculationService $dueService The due calculation service instance
     *
     * @return JsonResponse
     */
    public function summary(Customer $customer, DueCalculationService $dueService): JsonResponse
    {
        $dueCalculation = $dueService->calculateCustomerDues($customer);
        
        return response()->json([
            'id' => $customer->id,
            'name' => $customer->name,
            'shop_name' => $customer->shop_name,
            'phone' => $customer->phone,
            'email' => $customer->email,
            'full_address' => $customer->full_address,
            'outstanding_due' => $customer->outstanding_due,
            'last_five_orders' => $customer->getLastFiveOrders(),
            'due_calculation' => $dueCalculation,
        ]);
    }
}
