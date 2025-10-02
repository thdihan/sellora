<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $query = Budget::with(['creator', 'approver'])
                      ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('period_type')) {
            $query->where('period_type', $request->period_type);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $budgets = $query->paginate(12);

        // Calculate statistics
        $stats = [
            'total' => Budget::count(),
            'active' => Budget::where('status', 'active')->count(),
            'pending' => Budget::where('status', 'pending')->count(),
            'exceeded' => Budget::where('status', 'exceeded')->count(),
            'total_allocated' => Budget::where('status', 'active')->sum('total_amount'),
            'total_spent' => Budget::where('status', 'active')->sum('spent_amount'),
        ];

        $statuses = ['draft', 'pending', 'approved', 'active', 'completed', 'cancelled', 'exceeded'];
        $periodTypes = ['monthly', 'quarterly', 'half_yearly', 'yearly', 'custom'];

        return view('budgets.index', compact('budgets', 'stats', 'statuses', 'periodTypes'));
    }

    public function create()
    {
        $users = User::all();
        return view('budgets.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'period_type' => 'required|in:monthly,quarterly,half_yearly,yearly,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'auto_approve_limit' => 'nullable|numeric|min:0',
            'notification_threshold' => 'required|numeric|min:0|max:100',
            'categories' => 'nullable|array',
            'notes' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:monthly,quarterly,half_yearly,yearly'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['remaining_amount'] = $validated['total_amount'];
        $validated['status'] = 'draft';

        $budget = Budget::create($validated);

        return redirect()->route('budgets.show', $budget)
                        ->with('success', 'Budget created successfully!');
    }

    public function show(Budget $budget)
    {
        $budget->load(['creator', 'approver', 'expenses']);
        
        // Get budget utilization data for chart
        $utilizationData = [
            'spent' => $budget->spent_amount,
            'remaining' => $budget->remaining_amount
        ];

        // Get monthly spending trend
        $monthlySpending = $budget->expenses()
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get category breakdown
        $categoryBreakdown = $budget->expenses()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return view('budgets.show', compact('budget', 'utilizationData', 'monthlySpending', 'categoryBreakdown'));
    }

    public function edit(Budget $budget)
    {
        if (!in_array($budget->status, ['draft', 'pending'])) {
            return redirect()->route('budgets.show', $budget)
                           ->with('error', 'Cannot edit budget in current status.');
        }

        $users = User::all();
        return view('budgets.edit', compact('budget', 'users'));
    }

    public function update(Request $request, Budget $budget)
    {
        if (!in_array($budget->status, ['draft', 'pending'])) {
            return redirect()->route('budgets.show', $budget)
                           ->with('error', 'Cannot update budget in current status.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'period_type' => 'required|in:monthly,quarterly,half_yearly,yearly,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'total_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'auto_approve_limit' => 'nullable|numeric|min:0',
            'notification_threshold' => 'required|numeric|min:0|max:100',
            'categories' => 'nullable|array',
            'notes' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:monthly,quarterly,half_yearly,yearly'
        ]);

        // Recalculate remaining amount if total amount changed
        if ($budget->total_amount != $validated['total_amount']) {
            $validated['remaining_amount'] = $validated['total_amount'] - $budget->spent_amount;
        }

        $budget->update($validated);

        return redirect()->route('budgets.show', $budget)
                        ->with('success', 'Budget updated successfully!');
    }

    public function destroy(Budget $budget)
    {
        if (in_array($budget->status, ['active', 'completed'])) {
            return redirect()->route('budgets.index', request()->query())
                           ->with('error', 'Cannot delete budget in current status.');
        }

        $budget->delete();

        return redirect()->route('budgets.index', request()->query())
                        ->with('success', 'Budget deleted successfully!');
    }

    public function approve(Request $request, Budget $budget)
    {
        if ($budget->status !== 'pending') {
            return response()->json(['error' => 'Budget is not pending approval'], 400);
        }

        // Check role-based permissions for budget approval
        $user = Auth::user();
        if (!$this->canApproveBudget($user, $budget)) {
            return response()->json(['error' => 'Insufficient permissions to approve this budget'], 403);
        }

        $budget->approve(Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Budget approved successfully!',
            'status' => $budget->status
        ]);
    }

    public function activate(Budget $budget)
    {
        if ($budget->status !== 'approved') {
            return response()->json(['error' => 'Budget must be approved first'], 400);
        }

        // Check role-based permissions for budget activation
        $user = Auth::user();
        if (!$this->canActivateBudget($user, $budget)) {
            return response()->json(['error' => 'Insufficient permissions to activate this budget'], 403);
        }

        $budget->activate();

        return response()->json([
            'success' => true,
            'message' => 'Budget activated successfully!',
            'status' => $budget->status
        ]);
    }

    public function complete(Budget $budget)
    {
        if (!in_array($budget->status, ['active', 'exceeded'])) {
            return response()->json(['error' => 'Cannot complete budget in current status'], 400);
        }

        $budget->complete();

        return response()->json([
            'success' => true,
            'message' => 'Budget completed successfully!',
            'status' => $budget->status
        ]);
    }

    public function cancel(Budget $budget)
    {
        if (in_array($budget->status, ['completed', 'cancelled'])) {
            return response()->json(['error' => 'Cannot cancel budget in current status'], 400);
        }

        $budget->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Budget cancelled successfully!',
            'status' => $budget->status
        ]);
    }

    public function analytics()
    {
        $currentYear = Carbon::now()->year;
        
        // Budget vs Actual spending by month
        $monthlyData = Budget::selectRaw('
                MONTH(start_date) as month,
                SUM(total_amount) as budgeted,
                SUM(spent_amount) as actual
            ')
            ->whereYear('start_date', $currentYear)
            ->where('status', '!=', 'cancelled')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Budget utilization by period type
        $periodUtilization = Budget::selectRaw('
                period_type,
                AVG((spent_amount / total_amount) * 100) as avg_utilization
            ')
            ->where('total_amount', '>', 0)
            ->where('status', '!=', 'cancelled')
            ->groupBy('period_type')
            ->get();

        // Top spending categories
        $topCategories = Expense::selectRaw('
                category,
                SUM(amount) as total_spent
            ')
            ->whereHas('budget', function($q) {
                $q->where('status', '!=', 'cancelled');
            })
            ->groupBy('category')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // Budget status distribution
        $statusDistribution = Budget::selectRaw('
                status,
                COUNT(*) as count,
                SUM(total_amount) as total_amount
            ')
            ->groupBy('status')
            ->get();

        return view('budgets.analytics', compact(
            'monthlyData',
            'periodUtilization', 
            'topCategories',
            'statusDistribution'
        ));
    }

    public function duplicate(Budget $budget)
    {
        $newBudget = $budget->replicate();
        $newBudget->name = $budget->name . ' (Copy)';
        $newBudget->status = 'draft';
        $newBudget->spent_amount = 0;
        $newBudget->allocated_amount = 0;
        $newBudget->remaining_amount = $newBudget->total_amount;
        $newBudget->approved_by = null;
        $newBudget->approved_at = null;
        $newBudget->created_by = Auth::id();
        $newBudget->save();

        return redirect()->route('budgets.edit', $newBudget)
                        ->with('success', 'Budget duplicated successfully!');
    }

    public function submitForApproval(Budget $budget)
    {
        if ($budget->status !== 'draft') {
            return response()->json(['error' => 'Budget must be in draft status'], 400);
        }

        $budget->status = 'pending';
        $budget->save();

        return response()->json([
            'success' => true,
            'message' => 'Budget submitted for approval!',
            'status' => $budget->status
        ]);
    }

    public function updateSpending(Budget $budget)
    {
        $budget->updateSpentAmount();

        return response()->json([
            'success' => true,
            'message' => 'Budget spending updated!',
            'spent_amount' => $budget->spent_amount,
            'remaining_amount' => $budget->remaining_amount,
            'utilization_percentage' => $budget->utilization_percentage
        ]);
    }

    /**
     * Check if user can approve budgets based on role hierarchy
     */
    private function canApproveBudget($user, $budget)
    {
        // Admin and Manager roles can approve budgets
        if (in_array($user->role, ['admin', 'manager'])) {
            return true;
        }

        // Team Lead can approve budgets under certain amount threshold
        if ($user->role === 'team_lead' && $budget->total_amount <= 10000) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can activate budgets based on role hierarchy
     */
    private function canActivateBudget($user, $budget)
    {
        // Only Admin and Manager roles can activate budgets
        return in_array($user->role, ['admin', 'manager']);
    }
}
