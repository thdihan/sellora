<?php

/**
 * Expense Controller for managing expense operations
 *
 * @package App\Http\Controllers
 * @author  Sellora Team
 * @license MIT
 */

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * Class ExpenseController
 *
 * Handles all expense-related operations including CRUD operations,
 * file management, and expense reporting.
 */
class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request The HTTP request object
     * @return View
     */
    public function index(Request $request): View
    {
        $query = Expense::with(['user', 'approver']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('vendor', 'like', "%{$search}%")
                  ->orWhere('receipt_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $expenses = $query->paginate(15)->withQueryString();

        $categories = Expense::distinct()->pluck('category')->filter()->map(function($category) {
            return is_object($category) ? $category->name : $category;
        })->sort();
        $statuses = ['pending', 'approved', 'rejected', 'paid'];
        $priorities = ['low', 'medium', 'high'];

        return view('expenses.index', compact('expenses', 'categories', 'statuses', 'priorities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $categories = [
            'Travel', 'Meals', 'Office Supplies', 'Software', 'Marketing', 'Training', 'Equipment',
            'Sales Commission', 'Sales Incentives', 'Client Entertainment', 'Sales Materials', 
            'Trade Shows', 'Sales Training', 'CRM Software', 'Lead Generation', 'Sales Tools',
            'Team Building', 'Sales Meetings', 'Customer Visits', 'Sales Conferences', 
            'Promotional Items', 'Sales Literature', 'Other'
        ];
        $paymentMethods = ['Cash', 'Credit Card', 'Bank Transfer', 'Check', 'Online Payment'];
        
        return view('expenses.create', compact('categories', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The HTTP request object
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|size:3',
                'expense_date' => 'required|date',
                'receipt_number' => 'nullable|string|max:255',
                'vendor' => 'nullable|string|max:255',
                'priority' => 'required|in:low,medium,high',
                'notes' => 'nullable|string',
                'is_reimbursable' => 'boolean',
                'tax_amount' => 'nullable|numeric|min:0',
                'payment_method' => 'nullable|string|max:255',
                'reference_number' => 'nullable|string|max:255',
                'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('expenses/attachments', $filename, 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType(),
                ];
            }
        }

        $expense = Expense::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'expense_date' => $request->expense_date,
            'receipt_number' => $request->receipt_number,
            'vendor' => $request->vendor,
            'priority' => $request->priority,
            'notes' => $request->notes,
            'is_reimbursable' => $request->boolean('is_reimbursable', true),
            'tax_amount' => $request->tax_amount,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'attachments' => $attachments,
        ]);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Expense created successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param Expense $expense The expense model instance
     * @return View
     */
    public function show(Expense $expense): View
    {
        $expense->load(['user', 'approver']);
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Expense $expense The expense model instance
     * @return View
     */
    public function edit(Expense $expense): View
    {
        $categories = [
            'Travel', 'Meals', 'Office Supplies', 'Software', 'Marketing', 'Training', 'Equipment',
            'Sales Commission', 'Sales Incentives', 'Client Entertainment', 'Sales Materials', 
            'Trade Shows', 'Sales Training', 'CRM Software', 'Lead Generation', 'Sales Tools',
            'Team Building', 'Sales Meetings', 'Customer Visits', 'Sales Conferences', 
            'Promotional Items', 'Sales Literature', 'Other'
        ];
        $paymentMethods = ['Cash', 'Credit Card', 'Bank Transfer', 'Check', 'Online Payment'];
        
        return view('expenses.edit', compact('expense', 'categories', 'paymentMethods'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request The HTTP request object
     * @param Expense $expense The expense model instance
     * @return RedirectResponse
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|size:3',
                'expense_date' => 'required|date',
                'receipt_number' => 'nullable|string|max:255',
                'vendor' => 'nullable|string|max:255',
                'priority' => 'required|in:low,medium,high',
                'notes' => 'nullable|string',
                'is_reimbursable' => 'boolean',
                'tax_amount' => 'nullable|numeric|min:0',
                'payment_method' => 'nullable|string|max:255',
                'reference_number' => 'nullable|string|max:255',
                'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $attachments = $expense->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('expenses/attachments', $filename, 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType(),
                ];
            }
        }

        $expense->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'expense_date' => $request->expense_date,
            'receipt_number' => $request->receipt_number,
            'vendor' => $request->vendor,
            'priority' => $request->priority,
            'notes' => $request->notes,
            'is_reimbursable' => $request->boolean('is_reimbursable', true),
            'tax_amount' => $request->tax_amount,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'attachments' => $attachments,
        ]);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Expense updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Expense $expense The expense model instance
     * @return RedirectResponse
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        // Delete attachments from storage
        if ($expense->attachments) {
            foreach ($expense->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $expense->delete();

        return redirect()->route('expenses.index', request()->query())
            ->with('success', 'Expense deleted successfully!');
    }

    /**
     * Approve an expense.
     *
     * @param Request $request The HTTP request object
     * @param Expense $expense The expense model instance
     * @return RedirectResponse
     */
    public function approve(Request $request, Expense $expense): RedirectResponse
    {
        if (!$expense->canBeApproved()) {
            return redirect()->back()
                ->with('error', 'This expense cannot be approved.');
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes,
        ]);

        return redirect()->back()
            ->with('success', 'Expense approved successfully!');
    }

    /**
     * Reject an expense.
     *
     * @param Request $request The HTTP request object
     * @param Expense $expense The expense model instance
     * @return RedirectResponse
     */
    public function reject(Request $request, Expense $expense): RedirectResponse
    {
        if (!$expense->canBeApproved()) {
            return redirect()->back()
                ->with('error', 'This expense cannot be rejected.');
        }

        $validator = Validator::make(
            $request->all(),
            [
                'rejection_reason' => 'required|string|max:1000',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        $expense->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()
            ->with('success', 'Expense rejected successfully!');
    }

    /**
     * Mark expense as paid.
     *
     * @param Expense $expense The expense model instance
     * @return RedirectResponse
     */
    public function markAsPaid(Expense $expense): RedirectResponse
    {
        if (!$expense->isApproved()) {
            return redirect()->back()
                ->with('error', 'Only approved expenses can be marked as paid.');
        }

        $expense->update(['status' => 'paid']);

        return redirect()->back()
            ->with('success', 'Expense marked as paid successfully!');
    }

    /**
     * Download attachment.
     *
     * @param Expense $expense The expense model instance
     * @param int $index The attachment index
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadAttachment(Expense $expense, int $index)
    {
        if (!isset($expense->attachments[$index])) {
            abort(404, 'Attachment not found.');
        }

        $attachment = $expense->attachments[$index];
        $filePath = storage_path('app/public/' . $attachment['path']);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $attachment['name']);
    }

    /**
     * Remove attachment.
     *
     * @param Expense $expense The expense model instance
     * @param int $index The attachment index
     * @return JsonResponse
     */
    public function removeAttachment(Expense $expense, int $index): JsonResponse
    {
        if (!isset($expense->attachments[$index])) {
            return response()->json(['error' => 'Attachment not found.'], 404);
        }

        $attachments = $expense->attachments;
        $attachment = $attachments[$index];
        
        // Delete file from storage
        Storage::disk('public')->delete($attachment['path']);
        
        // Remove from array
        unset($attachments[$index]);
        $attachments = array_values($attachments); // Re-index array
        
        $expense->update(['attachments' => $attachments]);

        return response()->json(['success' => 'Attachment removed successfully.']);
    }
}
