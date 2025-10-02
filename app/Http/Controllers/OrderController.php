<?php

/**
 * Order Controller
 *
 * Handles order management operations including CRUD operations,
 * file attachments, and approval workflow.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 * @version  1.0
 * @since    2024
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderFile;
use App\Services\Mail\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * OrderController handles order management operations
 */
class OrderController extends Controller
{
    /**
     * Display a listing of orders
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'files']);

        // Role-based filtering
        if (Auth::user()->role && Auth::user()->role->name === 'MR') {
            $query->where('user_id', Auth::id());
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(
                function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%");
                }
            );
        }

        $orders = $query->latest()->paginate(15);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created order in storage
     *
     * @param Request $request The validated order request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate(
            [
                'customer_id' => 'nullable|exists:customers,id',
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'nullable|email|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'customer_address' => 'nullable|string|max:500',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0.01',
                'items.*.notes' => 'nullable|string|max:500',
                'discount' => 'nullable|numeric|min:0',
                'payment_method' => 'nullable|string|max:50',
                'attachments.*' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
                'vat_condition' => 'required|in:client_bears,company_bears',
                'tax_condition' => 'required|in:client_bears,company_bears'
            ]
        );
        
        // Calculate base amount from order items
        $subtotal = 0;
        foreach ($validatedData['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        $discount = $validatedData['discount'] ?? 0;
        $baseAmount = $subtotal - $discount;
        $validatedData['amount'] = $baseAmount;

        // Get VAT and TAX rates from settings
        $vatRate = \App\Models\Setting::where('key', 'vat_rate')->value('value') ?? 15;
        $taxRate = \App\Models\Setting::where('key', 'tax_rate')->value('value') ?? 5;
        
        // Calculate VAT and TAX amounts
        $vatAmount = ($baseAmount * $vatRate) / 100;
        $taxAmount = ($baseAmount * $taxRate) / 100;
        
        // Calculate total amount and net revenue based on conditions
        $totalAmount = $baseAmount;
        $netRevenue = $baseAmount;
        
        if ($validatedData['vat_condition'] === 'client_bears') {
            $totalAmount += $vatAmount;
        } else {
            $netRevenue -= $vatAmount;
        }
        
        if ($validatedData['tax_condition'] === 'client_bears') {
            $totalAmount += $taxAmount;
        } else {
            $netRevenue -= $taxAmount;
        }
        
        $validatedData['vat_amount'] = $vatAmount;
        $validatedData['tax_amount'] = $taxAmount;
        $validatedData['total_amount'] = $totalAmount;
        $validatedData['net_revenue'] = $netRevenue;
        $validatedData['user_id'] = Auth::id();
        $validatedData['status'] = Order::STATUS_PENDING;

        $order = Order::create($validatedData);

        // Create order items and update stock
        foreach ($validatedData['items'] as $item) {
            // Create order item
            $orderItem = $order->orderItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
                'notes' => $item['notes'] ?? null
            ]);

            // Reserve stock
            $stockReservationService = new \App\Services\StockReservationService();
            $stockReservationService->reserveStock($item['product_id'], $item['quantity'], $order->id);
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('order_attachments', 'public');
                OrderFile::create([
                    'order_id' => $order->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize()
                ]);
            }
        }

        // Send notification
        $notificationService = new NotificationService();
        $notificationService->sendOrderCreatedNotification($order);

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully with tax information.');
    }

    /**
     * Display the specified order
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $order->load('files');
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function edit(Order $order)
    {
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order in storage
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Order $order)
    {
        $validatedData = $request->validate(
            [
                'customer_name' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string',
                'attachments.*' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png'
            ]
        );

        $order->update($validatedData);

        // Handle new file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('order_attachments', 'public');
                OrderFile::create([
                    'order_id' => $order->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize()
                ]);
            }
        }

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified order from storage
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Order $order)
    {
        // Delete associated files
        foreach ($order->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $order->delete();

        return redirect()->route('orders.index', request()->query())
            ->with('success', 'Order deleted successfully.');
    }

    /**
     * Approve an order
     *
     * @param Request $request The request object
     * @param Order   $order   The order to approve
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, Order $order)
    {
        $request->validate(
            [
                'notes' => 'nullable|string|max:500'
            ]
        );

        $order->update(
            [
                'status' => Order::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'notes' => $request->notes
            ]
        );

        // Send notification
        $notificationService = new NotificationService();
        $notificationService->sendOrderApprovedNotification($order);

        return redirect()->back()
            ->with('success', 'Order approved successfully.');
    }

    /**
     * Download order attachment
     *
     * @param OrderFile $file
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAttachment(OrderFile $file)
    {
        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }
}
