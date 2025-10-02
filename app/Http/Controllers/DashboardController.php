<?php

/**
 * Dashboard Controller
 *
 * This file contains the DashboardController class which handles
 * dashboard display and statistics for the application.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Expense;
use App\Models\Bill;
use App\Models\User;
use App\Models\SalesTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Dashboard Controller Class
 *
 * Handles dashboard display, statistics calculation, and data aggregation
 * for different user roles including admin, manager, and regular users.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class DashboardController extends Controller
{
    /**
     * Display the dashboard
     *
     * Shows the main dashboard with statistics, charts, and recent activities
     * based on the authenticated user's role and permissions.
     *
     * @return \Illuminate\View\View The dashboard view with data
     */
    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please log in to access the dashboard.');
            }
            
            $role = $user->role->name ?? 'user';
            
            // Get date ranges for statistics
            $currentMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();
            
            // Base statistics available to all users with error handling
            $stats = [
                'total_orders' => $this->_getOrderStats($user, $role) ?? ['total' => 0, 'this_month' => 0, 'pending' => 0, 'approved' => 0, 'completed' => 0],
                'total_expenses' => $this->_getExpenseStats($user, $role) ?? ['total' => 0, 'total_amount' => 0, 'this_month_amount' => 0, 'pending' => 0, 'approved' => 0, 'paid' => 0],
                'total_bills' => $this->_getBillStats($user, $role) ?? ['total' => 0, 'total_amount' => 0, 'this_month_amount' => 0, 'pending' => 0, 'approved' => 0, 'paid' => 0],
                'monthly_budget' => $this->_getBudgetStats($user, $role) ?? ['allocated' => 0, 'used' => 0, 'remaining' => 0, 'percentage_used' => 0],
                'pending_approvals' => $this->_getPendingApprovals($user, $role) ?? 0,
            ];
            
            // Role-specific data
            $roleData = $this->_getRoleSpecificData($user, $role) ?? [];
            
            // Sales target data for all users
            $targetData = $this->_getTargetData($user) ?? [];
            
            // Chart data for the last 6 months
            $chartData = $this->_getChartData($user, $role) ?? ['months' => [], 'orders' => [], 'expenses' => [], 'bills' => []];
            
            // Recent activities
            $recentActivities = $this->_getRecentActivities($user, $role) ?? [];
            
            return view(
                'dashboard',
                compact('stats', 'roleData', 'targetData', 'chartData', 'recentActivities', 'user', 'role')
            );
        } catch (\Exception $e) {
            Log::error('Dashboard loading error: ' . $e->getMessage());
            
            // Return dashboard with default values on error
            $user = Auth::user();
            $role = $user->role ?? 'user';
            $stats = [
                'total_orders' => ['total' => 0, 'this_month' => 0, 'pending' => 0, 'approved' => 0, 'completed' => 0],
                'total_expenses' => ['total' => 0, 'total_amount' => 0, 'this_month_amount' => 0, 'pending' => 0, 'approved' => 0, 'paid' => 0],
                'total_bills' => ['total' => 0, 'total_amount' => 0, 'this_month_amount' => 0, 'pending' => 0, 'approved' => 0, 'paid' => 0],
                'monthly_budget' => ['allocated' => 0, 'used' => 0, 'remaining' => 0, 'percentage_used' => 0],
                'pending_approvals' => 0,
            ];
            $roleData = [];
            $chartData = ['months' => [], 'orders' => [], 'expenses' => [], 'bills' => []];
            $recentActivities = [];
            
            $targetData = [];
            return view(
                'dashboard',
                compact('stats', 'roleData', 'targetData', 'chartData', 'recentActivities', 'user', 'role')
            )->with('error', 'Some dashboard data could not be loaded. Please refresh the page.');
        }
    }
    
    /**
     * Get order statistics
     *
     * Calculates order statistics based on user role and permissions.
     *
     * @param \App\Models\User $user The authenticated user
     * @param string           $role The user's role
     *
     * @return array Order statistics array
     */
    private function _getOrderStats($user, $role)
    {
        $query = Order::query();
        
        if ($role !== 'Admin') {
            $query->where('user_id', $user->id);
        }
        
        return [
            'total' => $query->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'approved' => $query->where('status', 'approved')->count(),
            'completed' => $query->where('status', 'completed')->count(),
            'this_month' => $query->whereMonth('created_at', Carbon::now()->month)->count(),
        ];
    }
    
    /**
     * Get expense statistics
     *
     * Calculates expense statistics based on user role and permissions.
     *
     * @param \App\Models\User $user The authenticated user
     * @param string           $role The user's role
     *
     * @return array Expense statistics array
     */
    private function _getExpenseStats($user, $role)
    {
        $query = Expense::query();
        
        if ($role !== 'Admin') {
            $query->where('user_id', $user->id);
        }

        return [
            'total' => $query->count(),
            'pending' => $query->where('status', 'pending')->count(),
            'approved' => $query->where('status', 'approved')->count(),
            'paid' => $query->where('status', 'paid')->count(),
            'total_amount' => $query->sum('amount'),
            'this_month_amount' => $query->whereMonth('created_at', Carbon::now()->month)->sum('amount'),
        ];
    }
    
    /**
     * Get budget statistics
     *
     * Calculates budget statistics including allocated, used, and remaining amounts.
     *
     * @param \App\Models\User $user The authenticated user
     * @param string           $role The user's role
     *
     * @return array Budget statistics array
     */
    private function _getBudgetStats($user, $role)
    {
        // For now, return mock data - will be enhanced when Budget module is built
        $expenseStats = $this->_getExpenseStats($user, $role);
        $thisMonthAmount = $expenseStats['this_month_amount'];
        
        return [
            'allocated' => 10000,
            'used' => $thisMonthAmount,
            'remaining' => 10000 - $thisMonthAmount,
            'percentage_used' => ($thisMonthAmount / 10000) * 100,
        ];
    }
    
    /**
     * Get bill statistics
     *
     * Calculates bill statistics based on user role and permissions.
     *
     * @param \App\Models\User $user The authenticated user
     * @param string           $role The user's role
     *
     * @return array Bill statistics array
     */
    private function _getBillStats($user, $role)
    {
        $query = Bill::query();
        
        if ($role !== 'Admin') {
            $query->where('user_id', $user->id);
        }
        
        return [
            'total' => $query->count(),
            'pending' => $query->where('status', 'Pending')->count(),
            'approved' => $query->where('status', 'Approved')->count(),
            'paid' => $query->where('status', 'Paid')->count(),
            'total_amount' => $query->sum('amount'),
            'this_month_amount' => $query->whereMonth('created_at', Carbon::now()->month)->sum('amount'),
        ];
    }
    
    /**
     * Get pending approvals count
     *
     * Calculates the total number of pending approvals for admin users.
     *
     * @param \App\Models\User $user The authenticated user
     * @param string           $role The user's role
     *
     * @return int Total pending approvals count
     */
    private function _getPendingApprovals($user, $role)
    {
        if ($role !== 'Admin') {
            return 0;
        }
        
        $pendingOrders = Order::where('status', 'pending')->count();
        $pendingExpenses = Expense::where('status', 'pending')->count();
        $pendingBills = Bill::where('status', 'Pending')->count();
        
        return $pendingOrders + $pendingExpenses + $pendingBills;
    }
    
    /**
     * Get role-specific data
     *
     * Returns data specific to the user's role (admin, manager, or user).
     *
     * @param \App\Models\User $user The authenticated user
     * @param string           $role The user's role
     *
     * @return array Role-specific data array
     */
    private function _getRoleSpecificData($user, $role)
    {
        switch ($role) {
        case 'Author':
        case 'Admin':
            return [
                'total_users' => User::count(),
                'active_users' => User::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
                'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
                'monthly_revenue' => Order::where('status', 'completed')
                    ->whereMonth('created_at', Carbon::now()->month)->sum('total_amount'),
            ];
                
        case 'manager':
            return [
                'team_orders' => Order::count(), // Will be enhanced with team logic
                'team_expenses' => Expense::sum('amount'),
                'team_performance' => 85, // Mock data
            ];
                
        default:
            return [
                'personal_orders' => Order::where('user_id', $user->id)->count(),
                'personal_expenses' => Expense::where('user_id', $user->id)->sum('amount'),
                'completion_rate' => 92, // Mock data
            ];
        }
    }
    
    /**
     * Get chart data
     *
     * Generates chart data for the last 6 months including orders, expenses, and bills.
     *
     * @param \App\Models\User $user The authenticated user
     * @param string           $role The user's role
     *
     * @return array Chart data array with months and data series
     */
    private function _getChartData($user, $role)
    {
        $months = [];
        $orderData = [];
        $expenseData = [];
        $billData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $orderQuery = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            $expenseQuery = Expense::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
            $billQuery = Bill::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month);
                
            if ($role !== 'Admin') {
                $orderQuery->where('user_id', $user->id);
                $expenseQuery->where('user_id', $user->id);
                $billQuery->where('user_id', $user->id);
            }
            
            $orderData[] = $orderQuery->count();
            $expenseData[] = $expenseQuery->sum('amount');
            $billData[] = $billQuery->sum('amount');
        }
        
        return [
            'months' => $months,
            'orders' => $orderData,
            'expenses' => $expenseData,
            'bills' => $billData,
        ];
    }
    
    /**
     * Get target data for the user
     *
     * Retrieves sales target achievement data for the authenticated user.
     *
     * @param \App\Models\User $user The authenticated user
     *
     * @return array Target achievement data array
     */
    private function _getTargetData($user)
    {
        try {
            // Check if user has assigned targets
            $hasTarget = SalesTarget::where('assigned_to_user_id', $user->id)
                ->where('target_year', date('Y'))
                ->exists();
            
            if (!$hasTarget) {
                return [
                    'has_target' => false,
                    'message' => 'No sales targets assigned for this year.'
                ];
            }
            
            // Get achievement data using the SalesTarget model method
            $achievementData = SalesTarget::getAchievementData($user);
            
            return [
                'has_target' => true,
                'weekly' => [
                    'target' => $achievementData['weekly']['target'] ?? 0,
                    'actual' => $achievementData['weekly']['actual'] ?? 0,
                    'achievement' => $achievementData['weekly']['achievement'] ?? 0,
                    'percentage' => $achievementData['weekly']['target'] > 0 
                        ? round(($achievementData['weekly']['actual'] / $achievementData['weekly']['target']) * 100, 1) 
                        : 0
                ],
                'monthly' => [
                    'target' => $achievementData['monthly']['target'] ?? 0,
                    'actual' => $achievementData['monthly']['actual'] ?? 0,
                    'achievement' => $achievementData['monthly']['achievement'] ?? 0,
                    'percentage' => $achievementData['monthly']['target'] > 0 
                        ? round(($achievementData['monthly']['actual'] / $achievementData['monthly']['target']) * 100, 1) 
                        : 0
                ],
                'yearly' => [
                    'target' => $achievementData['yearly']['target'] ?? 0,
                    'actual' => $achievementData['yearly']['actual'] ?? 0,
                    'achievement' => $achievementData['yearly']['achievement'] ?? 0,
                    'percentage' => $achievementData['yearly']['target'] > 0 
                        ? round(($achievementData['yearly']['actual'] / $achievementData['yearly']['target']) * 100, 1) 
                        : 0
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error getting target data: ' . $e->getMessage());
            return [
                'has_target' => false,
                'error' => 'Unable to load target data.'
            ];
        }
    }
    
    /**
     * Get recent activities
     *
     * Retrieves and formats recent activities including orders, expenses, and bills.
     *
     * @param \App\Models\User $user The authenticated user
     * @param string           $role The user's role
     *
     * @return array Recent activities array
     */
    private function _getRecentActivities($user, $role)
    {
        $activities = [];
        
        // Recent orders
        $orderQuery = Order::with('user')->latest()->limit(5);
        if ($role !== 'Admin') {
            $orderQuery->where('user_id', $user->id);
        }
        
        foreach ($orderQuery->get() as $order) {
            $activities[] = [
                'type' => 'order',
                'icon' => 'fas fa-shopping-cart',
                'color' => 'primary',
                'title' => 'Order: ' . $order->title,
                'description' => 'Status: ' . ucfirst($order->status),
                'time' => $order->created_at->diffForHumans(),
                'user' => $order->user->name,
            ];
        }
        
        // Recent expenses
        $expenseQuery = Expense::with('user')->latest()->limit(3);
        if ($role !== 'Admin') {
            $expenseQuery->where('user_id', $user->id);
        }
        
        foreach ($expenseQuery->get() as $expense) {
            $activities[] = [
                'type' => 'expense',
                'icon' => 'fas fa-receipt',
                'color' => 'success',
                'title' => 'Expense: ' . $expense->title,
                'description' => $expense->currency . ' ' . number_format($expense->amount, 2),
                'time' => $expense->created_at->diffForHumans(),
                'user' => $expense->user->name,
            ];
        }
        
        // Recent bills
        $billQuery = Bill::with('user')->latest()->limit(3);
        if ($role !== 'Admin') {
            $billQuery->where('user_id', $user->id);
        }
        
        foreach ($billQuery->get() as $bill) {
            $activities[] = [
                'type' => 'bill',
                'icon' => 'fas fa-file-invoice-dollar',
                'color' => 'warning',
                'title' => 'Bill: ' . ($bill->purpose ?: 'Bill #' . $bill->id),
                'description' => '৳' . number_format($bill->amount, 2) . ' - ' . ucfirst($bill->status),
                'time' => $bill->created_at->diffForHumans(),
                'user' => $bill->user->name,
            ];
        }
        
        // Sort by creation time and limit to 10
        usort(
            $activities,
            function ($a, $b) {
                return strtotime($b['time']) - strtotime($a['time']);
            }
        );
        
        return array_slice($activities, 0, 10);
    }
}
