<?php

/**
 * Bill Controller
 *
 * This file contains the BillController class which handles all bill-related
 * operations including CRUD operations, approval workflow, and file management.
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillFile;
use App\Models\User;
use App\Services\BillFileService;
use App\Services\Mail\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Bill Controller
 *
 * Handles all bill-related operations including CRUD operations,
 * approval workflow, and file management.
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class BillController extends Controller
{
    protected BillFileService $fileService;

    /**
     * Constructor
     *
     * @param BillFileService $fileService The bill file service instance
     */
    public function __construct(BillFileService $fileService)
    {
        $this->fileService = $fileService;
    }
    /**
     * Display a listing of bills
     *
     * @param Request $request The HTTP request object
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $query = Bill::with(['user', 'approver']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(
                function ($q) use ($search) {
                    $q->where('purpose', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('vendor', 'like', "%{$search}%")
                        ->orWhere('receipt_number', 'like', "%{$search}%")
                        ->orWhereHas(
                            'user',
                            function ($userQuery) use ($search) {
                                $userQuery->where('name', 'like', "%{$search}%");
                            }
                        );
                }
            );
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

        // Filter by amount range
        if ($request->filled('amount_from')) {
            $query->where('amount', '>=', $request->amount_from);
        }
        if ($request->filled('amount_to')) {
            $query->where('amount', '<=', $request->amount_to);
        }

        $bills = $query->orderBy('created_at', 'desc')->paginate(12);

        // Calculate statistics
        $stats = [
            'total' => Bill::count(),
            'pending' => Bill::where('status', 'Pending')->count(),
            'approved' => Bill::where('status', 'Approved')->count(),
            'paid' => Bill::where('status', 'Paid')->count(),
            'rejected' => Bill::where('status', 'Rejected')->count(),
            'total_amount' => Bill::whereIn('status', ['Approved', 'Paid'])->sum('amount'),
            'pending_amount' => Bill::where('status', 'Pending')->sum('amount'),
        ];

        $statuses = ['Pending', 'Approved', 'Forwarded', 'Paid', 'Rejected'];
        $categories = [
            'Travel', 'Meals', 'Office Supplies', 'Software', 'Marketing', 'Training', 'Equipment',
            'Sales Commission', 'Sales Incentives', 'Client Entertainment', 'Sales Materials', 
            'Trade Shows', 'Sales Training', 'CRM Software', 'Lead Generation', 'Sales Tools',
            'Team Building', 'Sales Meetings', 'Customer Visits', 'Sales Conferences', 
            'Promotional Items', 'Sales Literature', 'Other'
        ];
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];

        return view(
            'bills.index',
            compact('bills', 'stats', 'statuses', 'categories', 'priorities')
        );
    }

    /**
     * Show the form for creating a new bill
     *
     * @return \Illuminate\View\View
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
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];
        
        return view(
            'bills.create',
            compact('categories', 'paymentMethods', 'priorities')
        );
    }

    /**
     * Store a newly created bill in storage
     *
     * @param Request $request The HTTP request object
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'vendor' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'expense_date' => 'required|date',
            'category' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $bill = Bill::create([
            'user_id' => Auth::id(),
            'purpose' => $request->purpose,
            'amount' => $request->amount,
            'description' => $request->description,
            'vendor' => $request->vendor,
            'receipt_number' => $request->receipt_number,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'payment_method' => $request->payment_method,
            'priority' => $request->priority,
            'notes' => $request->notes,
            'status' => 'Pending',
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $this->fileService->uploadFiles($request->file('attachments'), $bill->id);
        }

        // Send approval request notification
        $notificationService = new NotificationService();
        $notificationService->sendApprovalRequestNotification(
            Auth::id(),
            'bill',
            $bill->id,
            [
                'submitter_name' => Auth::user()->name,
                'purpose' => $bill->purpose,
                'amount' => $bill->amount,
                'category' => $bill->category
            ]
        );

        return redirect()
            ->route('bills.index')
            ->with('success', 'Bill created successfully!');
    }

    /**
     * Display the specified bill
     *
     * @param Bill $bill The bill model instance
     * @return \Illuminate\View\View
     */
    public function show(Bill $bill): View
    {
        $bill->load(['user', 'approver', 'files', 'approvals.actor']);
        
        return view('bills.show', compact('bill'));
    }

    /**
     * Show the form for editing the specified bill
     *
     * @param Bill $bill The bill model instance
     * @return \Illuminate\View\View
     */
    public function edit(Bill $bill): View
    {
        // Check if bill can be edited
        if (!$bill->canBeEdited()) {
            return redirect()
                ->route('bills.show', $bill)
                ->with('error', 'This bill cannot be edited in its current status.');
        }

        $categories = [
            'Travel', 'Meals', 'Office Supplies', 'Software', 'Marketing', 'Training', 'Equipment',
            'Sales Commission', 'Sales Incentives', 'Client Entertainment', 'Sales Materials', 
            'Trade Shows', 'Sales Training', 'CRM Software', 'Lead Generation', 'Sales Tools',
            'Team Building', 'Sales Meetings', 'Customer Visits', 'Sales Conferences', 
            'Promotional Items', 'Sales Literature', 'Other'
        ];
        $paymentMethods = ['Cash', 'Credit Card', 'Bank Transfer', 'Check', 'Online Payment'];
        $priorities = ['Low', 'Medium', 'High', 'Urgent'];
        
        $bill->load('files');
        
        return view(
            'bills.edit',
            compact('bill', 'categories', 'paymentMethods', 'priorities')
        );
    }

    /**
     * Update the specified bill in storage
     *
     * @param Request $request The HTTP request object
     * @param Bill $bill The bill model instance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Bill $bill): RedirectResponse
    {
        // Check if bill can be edited
        if (!$bill->canBeEdited()) {
            return redirect()
                ->route('bills.show', $bill)
                ->with('error', 'This bill cannot be edited in its current status.');
        }

        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'vendor' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255',
            'expense_date' => 'required|date',
            'category' => 'required|string|max:255',
            'payment_method' => 'required|string|max:255',
            'priority' => 'required|in:Low,Medium,High,Urgent',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $bill->update([
            'purpose' => $request->purpose,
            'amount' => $request->amount,
            'description' => $request->description,
            'vendor' => $request->vendor,
            'receipt_number' => $request->receipt_number,
            'expense_date' => $request->expense_date,
            'category' => $request->category,
            'payment_method' => $request->payment_method,
            'priority' => $request->priority,
            'notes' => $request->notes,
        ]);

        // Handle new file attachments
        if ($request->hasFile('attachments')) {
            $this->fileService->uploadFiles($request->file('attachments'), $bill->id);
        }

        return redirect()
            ->route('bills.show', $bill)
            ->with('success', 'Bill updated successfully!');
    }

    /**
     * Remove the specified bill from storage
     *
     * @param Bill $bill The bill model instance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Bill $bill): RedirectResponse
    {
        // Check if bill can be deleted
        if (!$bill->canBeEdited()) {
            return redirect()
                ->route('bills.index', request()->query())
                ->with('error', 'This bill cannot be deleted in its current status.');
        }

        // Delete associated files
        foreach ($bill->files as $file) {
            $file->deleteFile();
            $file->delete();
        }

        $bill->delete();

        return redirect()
            ->route('bills.index', request()->query())
            ->with('success', 'Bill deleted successfully!');
    }

    /**
     * Approve a bill
     *
     * @param Request $request The HTTP request object
     * @param Bill $bill The bill model instance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, Bill $bill): RedirectResponse
    {
        if (!$bill->canBeApproved()) {
            return redirect()
                ->back()
                ->with('error', 'This bill cannot be approved in its current status.');
        }

        $bill->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Log the approval
        $bill->approvals()->create([
            'entity_type' => 'bill',
            'entity_id' => $bill->id,
            'from_role' => 'user',
            'to_role' => 'manager',
            'action' => 'approve',
            'remarks' => $request->remarks,
            'acted_by' => Auth::id(),
        ]);

        // Send approval notification to submitter
        $notificationService = new NotificationService();
        $notificationService->sendApprovalRequestNotification(
            $bill->user_id,
            'bill',
            $bill->id,
            [
                'approved_by' => Auth::user()->name,
                'purpose' => $bill->purpose,
                'amount' => $bill->amount,
                'status' => 'approved'
            ]
        );

        return redirect()
            ->back()
            ->with('success', 'Bill approved successfully!');
    }

    /**
     * Reject a bill
     *
     * @param Request $request The HTTP request object
     * @param Bill $bill The bill model instance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, Bill $bill): RedirectResponse
    {
        if (!$bill->canBeRejected()) {
            return redirect()
                ->back()
                ->with('error', 'This bill cannot be rejected in its current status.');
        }

        $validator = Validator::make($request->all(), [
            'rejected_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator);
        }

        $bill->update([
            'status' => 'Rejected',
            'rejected_reason' => $request->rejected_reason,
        ]);

        // Log the rejection
        $bill->approvals()->create([
            'entity_type' => 'bill',
            'entity_id' => $bill->id,
            'from_role' => 'manager',
            'to_role' => 'user',
            'action' => 'reject',
            'remarks' => $request->rejected_reason,
            'acted_by' => Auth::id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Bill rejected successfully!');
    }

    /**
     * Mark the specified bill as paid
     *
     * @param Request $request The HTTP request object
     * @param Bill $bill The bill model instance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsPaid(Request $request, Bill $bill): RedirectResponse
    {
        if (!$bill->canBeMarkedAsPaid()) {
            return redirect()
                ->back()
                ->with('error', 'This bill cannot be marked as paid in its current status.');
        }

        $bill->update([
            'status' => 'Paid',
        ]);

        // Log the payment
        $bill->approvals()->create([
            'entity_type' => 'bill',
            'entity_id' => $bill->id,
            'from_role' => 'manager',
            'to_role' => 'finance',
            'action' => 'approve',
            'remarks' => 'Marked as paid',
            'acted_by' => Auth::id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Bill marked as paid successfully!');
    }

    /**
     * Download attachment file
     *
     * @param Bill $bill The bill model instance
     * @param int $index The file index
     * @return mixed Download response or redirect
     */
    public function downloadAttachment(Bill $bill, int $index): mixed
    {
        $files = $bill->files;
        
        if (!isset($files[$index])) {
            return redirect()
                ->back()
                ->with('error', 'File not found.');
        }

        $file = $files[$index];
        
        if (!$file->fileExists()) {
            return redirect()
                ->back()
                ->with('error', 'File not found in storage.');
        }

        return Storage::download($file->file_path, $file->original_name);
    }

    /**
     * Remove attachment file
     *
     * @param Bill $bill The bill model instance
     * @param int $index The file index
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeAttachment(Bill $bill, int $index): RedirectResponse
    {
        if (!$bill->canBeEdited()) {
            return redirect()
                ->back()
                ->with('error', 'Cannot remove attachments from this bill in its current status.');
        }

        $files = $bill->files;
        
        if (!isset($files[$index])) {
            return redirect()
                ->back()
                ->with('error', 'File not found.');
        }

        $file = $files[$index];
        
        // Delete file using service
        $this->fileService->deleteFile($file);

        return redirect()
            ->back()
            ->with('success', 'Attachment removed successfully!');
    }
}