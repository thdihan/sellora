<?php

/**
 * Reports Controller
 *
 * This controller handles all reporting functionality for the Sellora application,
 * including sales reports, expense reports, visit reports, budget reports,
 * custom report generation, and data export capabilities.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0.0
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Visit;
use App\Models\Budget;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class ReportsController
 *
 * Manages comprehensive reporting functionality across all modules
 * including analytics, data visualization, and export capabilities.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @version  1.0.0
 * @link     https://sellora.com
 * @since    2024-01-01
 */
class ReportsController extends Controller
{
    /**
     * Display the main reports dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = $this->_getDashboardStats();
        $recentActivity = $this->_getRecentActivity();
        return view('reports.index', compact('stats', 'recentActivity'));
    }

    /**
     * Get dashboard statistics
     *
     * @return array
     */
    private function _getDashboardStats()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Sales statistics
        $totalSales = Order::sum('total_amount') ?? 0;
        $totalSalesCount = Order::count();
        $thisMonthSales = Order::where('created_at', '>=', $currentMonth)
            ->sum('total_amount') ?? 0;
        $lastMonthSales = Order::whereBetween(
            'created_at',
            [$lastMonth, $lastMonthEnd]
        )->sum('total_amount') ?? 0;

        // Expenses statistics
        $totalExpenses = Expense::sum('amount') ?? 0;
        $totalExpensesCount = Expense::count();
        $thisMonthExpenses = Expense::where('created_at', '>=', $currentMonth)
            ->sum('amount') ?? 0;
        $lastMonthExpenses = Expense::whereBetween(
            'created_at',
            [$lastMonth, $lastMonthEnd]
        )->sum('amount') ?? 0;

        // Visits statistics
        $totalVisits = Visit::count();
        $completedVisits = Visit::where('status', 'completed')->count();
        $successRate = $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100, 1) : 0;

        // Budget statistics
        $activeBudgets = Budget::where('status', 'active')->get();
        $activeBudgetsCount = $activeBudgets->count();
        $totalBudgetAmount = $activeBudgets->sum('amount') ?? 0;
        $totalSpentAmount = $activeBudgets->sum('spent_amount') ?? 0;
        $budgetUtilization = $totalBudgetAmount > 0 ? round(($totalSpentAmount / $totalBudgetAmount) * 100, 1) : 0;

        return [
            'total_sales' => [
                'amount' => $totalSales,
                'count' => $totalSalesCount,
                'this_month' => $thisMonthSales,
                'last_month' => $lastMonthSales
            ],
            'total_expenses' => [
                'amount' => $totalExpenses,
                'count' => $totalExpensesCount,
                'this_month' => $thisMonthExpenses,
                'last_month' => $lastMonthExpenses
            ],
            'total_visits' => [
                'count' => $totalVisits,
                'completed' => $completedVisits,
                'success_rate' => $successRate
            ],
            'active_budgets' => [
                'count' => $activeBudgetsCount,
                'total_amount' => $totalBudgetAmount,
                'utilization' => $budgetUtilization
            ]
        ];
    }

    /**
     * Display sales reports with analytics
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return \Illuminate\View\View
     */
    public function sales(Request $request)
    {
        $salesData = $this->_getSalesData($request);
        return view('reports.sales', compact('salesData'));
    }

    /**
     * Display expense reports with analytics
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return \Illuminate\View\View
     */
    public function expenses(Request $request)
    {
        $expenseData = $this->_getExpenseData($request);
        return view('reports.expenses', compact('expenseData'));
    }

    /**
     * Display visit reports with analytics
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return \Illuminate\View\View
     */
    public function visits(Request $request)
    {
        $visitData = $this->_getVisitData($request);
        return view('reports.visits', compact('visitData'));
    }

    /**
     * Display budget reports with analytics
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return \Illuminate\View\View
     */
    public function budgets(Request $request)
    {
        $budgetData = $this->_getBudgetData($request);
        return view('reports.budgets', compact('budgetData'));
    }

    /**
     * Display custom report builder interface
     *
     * @return \Illuminate\View\View
     */
    public function custom()
    {
        return view('reports.custom');
    }

    /**
     * Generate custom report based on user configuration
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateCustom(Request $request)
    {
        $config = $request->all();
        $data = $this->_buildCustomReport($config);
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'chart_data' => $this->_prepareChartData($data, $config)
        ]);
    }

    /**
     * Export report data in various formats
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $type = $request->input('type');
        $format = $request->input('format', 'pdf');
        $dateRange = $request->input('date_range');
        
        $data = $this->_getExportData($type, $dateRange, $request);
        
        switch ($format) {
            case 'pdf':
                return $this->_exportToPdf($data, $type);
            case 'excel':
                return $this->_exportToExcel($data, $type);
            case 'csv':
                return $this->_exportToCsv($data, $type);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    /**
     * Get sales data for reports
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return array
     */
    private function _getSalesData(Request $request)
    {
        $dateRange = $this->_getDateRange($request);
        $userIds = $this->_getAccessibleUserIds();
        
        $orders = Order::whereBetween('created_at', $dateRange)
            ->whereIn('user_id', $userIds)
            ->get();
            
        $totalAmount = $orders->sum('total_amount') ?? 0;
        $totalOrders = $orders->count();
        $averageOrder = $totalOrders > 0 ? $totalAmount / $totalOrders : 0;
        $pendingOrders = $orders->where('status', 'pending')->count();
        
        return [
            'stats' => [
                'total_orders' => $totalOrders,
                'total_amount' => $totalAmount,
                'average_order' => $averageOrder,
                'pending_orders' => $pendingOrders
            ],
            'orders' => $orders,
            'time_series' => $this->_prepareSalesTimeSeriesData($orders),
            'status_breakdown' => $this->_prepareSalesStatusData($orders),
            'payment_breakdown' => $this->_prepareSalesPaymentData($orders),
            'top_customers' => $this->_prepareTopCustomersData($orders)
        ];
    }

    /**
     * Get expense data for reports
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return array
     */
    private function _getExpenseData(Request $request)
    {
        $dateRange = $this->_getDateRange($request);
        $userIds = $this->_getAccessibleUserIds();
        
        $expenses = Expense::whereBetween('created_at', $dateRange)
            ->whereIn('user_id', $userIds)
            ->with(['user'])
            ->get();
            
        return [
            'stats' => [
                'total_amount' => $expenses->sum('amount'),
                'total_count' => $expenses->count(),
                'average_amount' => $expenses->avg('amount')
            ],
            'expenses' => $expenses,
            'time_series' => $this->_prepareExpenseTimeSeriesData($expenses),
            'category_breakdown' => $this->_prepareExpenseCategoryData($expenses),
            'status_breakdown' => $this->_prepareExpenseStatusData($expenses)
        ];
    }

    /**
     * Get visit data for reports
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return array
     */
    private function _getVisitData(Request $request)
    {
        $dateRange = $this->_getDateRange($request);
        $userIds = $this->_getAccessibleUserIds();
        
        $visits = Visit::whereBetween('created_at', $dateRange)
            ->whereIn('user_id', $userIds)
            ->get();
            
        return [
            'stats' => [
                'total_visits' => $visits->count(),
                'completed_visits' => $visits->where('status', 'completed')->count(),
                'pending_visits' => $visits->where('status', 'pending')->count(),
                'scheduled_visits' => $visits->where('status', 'scheduled')->count(),
                'cancelled_visits' => $visits->where('status', 'cancelled')->count(),
                'success_rate' => $visits->count() > 0 ? round(($visits->where('status', 'completed')->count() / $visits->count()) * 100, 1) : 0
            ],
            'visits' => $visits,
            'chart_data' => $this->_prepareVisitChartData($visits),
            'type_breakdown' => $this->_prepareVisitTypeBreakdown($visits),
            'status_breakdown' => $this->_prepareVisitStatusBreakdown($visits),
            'time_series' => $this->_prepareVisitTimeSeries($visits)
        ];
    }

    /**
     * Get budget data for reports
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return array
     */
    private function _getBudgetData(Request $request)
    {
        $dateRange = $this->_getDateRange($request);
        $userIds = $this->_getAccessibleUserIds();
        
        $budgets = Budget::whereBetween('created_at', $dateRange)
            ->whereIn('user_id', $userIds)
            ->with(['category'])
            ->get();
            
        $totalBudget = $budgets->sum('amount');
        $totalSpent = $budgets->sum('spent_amount');
        $utilizationRate = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100, 2) : 0;
        
        // Prepare category breakdown data
        $categoryBreakdown = $budgets->groupBy('category.name')->map(
            function ($categoryBudgets, $categoryName) {
                $budgetAmount = $categoryBudgets->sum('amount');
                $spentAmount = $categoryBudgets->sum('spent_amount');
                $variance = $budgetAmount - $spentAmount;

                return [
                    'category' => $categoryName ?? 'Uncategorized',
                    'budget' => $budgetAmount,
                    'actual' => $spentAmount,
                    'variance' => $variance,
                    'variance_percentage' => $budgetAmount > 0 ? round(($variance / $budgetAmount) * 100, 2) : 0
                ];
            }
        )->values();
        
        return [
            'stats' => [
                'total_budget' => $totalBudget,
                'total_spent' => $totalSpent,
                'remaining_budget' => $totalBudget - $totalSpent,
                'utilization_rate' => $utilizationRate
            ],
            'budgets' => $budgets,
            'category_breakdown' => $categoryBreakdown,
            'chart_data' => $this->_prepareBudgetChartData($budgets)
        ];
    }

    /**
     * Get date range from request parameters
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     *
     * @return array
     */
    private function _getDateRange(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        return [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ];
    }

    /**
     * Get accessible user IDs based on current user's role
     *
     * @return array
     */
    private function _getAccessibleUserIds()
    {
        $user = Auth::user();
        
        if (!$user || !$user->role) {
            return [];
        }
        
        switch ($user->role->name) {
        case 'Author':
        case 'Admin':
        case 'NSM':
        case 'NSM+':
            return User::pluck('id')->toArray();
            
        case 'ZSM':
            return User::where('zone_id', $user->zone_id)
                ->pluck('id')->toArray();

        case 'ASM':
            return User::where('area_id', $user->area_id)
                ->pluck('id')->toArray();
                
        case 'MR':
        default:
            return [$user->id];
        }
    }
    
    /**
     * Prepare sales chart data
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders The orders collection
     *
     * @return array
     */
    private function _prepareSalesChartData($orders)
    {
        return [
            'daily_sales' => $orders->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })->map(function ($dayOrders) {
                return $dayOrders->sum('total_amount');
            }),
            'status_breakdown' => $orders->groupBy('status')->map->count()
        ];
    }

    /**
     * Prepare sales time series data
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders The orders collection
     *
     * @return array
     */
    private function _prepareSalesTimeSeriesData($orders)
    {
        return $orders->groupBy(
            function ($order) {
                return $order->created_at->format('Y-m-d');
            }
        )->map(
            function ($dayOrders, $date) {
                return [
                    'period' => $date,
                    'value' => $dayOrders->sum('total_amount')
                ];
            }
        )->values()->toArray();
    }

    /**
     * Prepare sales status breakdown data
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders The orders collection
     *
     * @return array
     */
    private function _prepareSalesStatusData($orders)
    {
        return $orders->groupBy('status')->map(
            function ($statusOrders, $status) {
                return (object) [
                    'status' => $status,
                    'count' => $statusOrders->count(),
                    'amount' => $statusOrders->sum('total_amount')
                ];
            }
        )->values();
    }

    /**
     * Prepare sales payment breakdown data
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders The orders collection
     *
     * @return array
     */
    private function _prepareSalesPaymentData($orders)
    {
        return $orders->groupBy('payment_status')->map(function ($paymentOrders, $paymentStatus) {
            return (object) [
                'payment_status' => $paymentStatus ?: 'pending',
                'count' => $paymentOrders->count(),
                'amount' => $paymentOrders->sum('total_amount')
            ];
        })->values();
    }

    /**
     * Prepare top customers data
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders The orders collection
     *
     * @return array
     */
    private function _prepareTopCustomersData($orders)
    {
        return $orders->groupBy('customer_name')
            ->map(function ($customerOrders) {
                $customerName = $customerOrders->first()->customer_name;
                return [
                    'name' => $customerName ?: 'Unknown',
                    'email' => '',
                    'orders_count' => $customerOrders->count(),
                    'total_amount' => $customerOrders->sum('total_amount')
                ];
            })
            ->sortByDesc('total_amount')
            ->take(5)
            ->values();
    }

    /**
     * Prepare expense time series data
     *
     * @param \Illuminate\Database\Eloquent\Collection $expenses The expenses collection
     *
     * @return array
     */
    private function _prepareExpenseTimeSeriesData($expenses)
    {
        return $expenses->groupBy(function ($expense) {
            return $expense->created_at->format('Y-m-d');
        })->map(function ($dayExpenses, $date) {
            return [
                'period' => $date,
                'value' => $dayExpenses->sum('amount')
            ];
        })->values()->toArray();
    }

    /**
     * Prepare expense category breakdown data
     *
     * @param \Illuminate\Database\Eloquent\Collection $expenses The expenses collection
     *
     * @return array
     */
    private function _prepareExpenseCategoryData($expenses)
    {
        return $expenses->groupBy(function ($expense) {
            return $expense->category ?: 'Uncategorized';
        })->map(function ($categoryExpenses, $category) {
            return (object) [
                'category' => $category,
                'count' => $categoryExpenses->count(),
                'amount' => $categoryExpenses->sum('amount')
            ];
        })->values();
    }

    /**
     * Prepare expense status breakdown data
     *
     * @param \Illuminate\Database\Eloquent\Collection $expenses The expenses collection
     *
     * @return array
     */
    private function _prepareExpenseStatusData($expenses)
    {
        return $expenses->groupBy('status')->map(function ($statusExpenses, $status) {
            return (object) [
                'status' => $status,
                'count' => $statusExpenses->count(),
                'amount' => $statusExpenses->sum('amount')
            ];
        })->values();
    }

    /**
     * Prepare visit chart data
     *
     * @param \Illuminate\Database\Eloquent\Collection $visits The visits collection
     *
     * @return array
     */
    private function _prepareVisitChartData($visits)
    {
        return [
            'daily_visits' => $visits->groupBy(function ($visit) {
                return $visit->created_at->format('Y-m-d');
            })->map->count(),
            'status_breakdown' => $visits->groupBy('status')->map->count()
        ];
    }

    /**
     * Prepare visit type breakdown data
     *
     * @param \Illuminate\Database\Eloquent\Collection $visits The visits collection
     *
     * @return array
     */
    private function _prepareVisitTypeBreakdown($visits)
    {
        return $visits->groupBy('visit_type')
            ->map(
                function ($typeVisits, $type) {
                    return (object) [
                        'visit_type' => $type ?? 'general',
                        'count' => $typeVisits->count()
                    ];
                }
            )
            ->values()
            ->toArray();
    }

    /**
     * Prepare visit status breakdown data
     *
     * @param \Illuminate\Database\Eloquent\Collection $visits The visits collection
     *
     * @return array
     */
    private function _prepareVisitStatusBreakdown($visits)
    {
        return $visits->groupBy('status')
            ->map(function ($statusVisits, $status) {
                return (object) [
                    'status' => $status ?? 'pending',
                    'count' => $statusVisits->count()
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Prepare visit time series data
     *
     * @param \Illuminate\Database\Eloquent\Collection $visits The visits collection
     *
     * @return array
     */
    private function _prepareVisitTimeSeries($visits)
    {
        $timeSeries = $visits->groupBy(function ($visit) {
            return $visit->created_at->format('Y-m-d');
        })
        ->map(function ($dayVisits, $date) {
            return (object) [
                'period' => Carbon::parse($date)->format('M d, Y'),
                'value' => $dayVisits->count()
            ];
        })
        ->sortKeys()
        ->values()
        ->toArray();

        return $timeSeries;
    }

    /**
     * Prepare budget chart data
     *
     * @param \Illuminate\Database\Eloquent\Collection $budgets The budgets collection
     *
     * @return array
     */
    private function _prepareBudgetChartData($budgets)
    {
        return [
            'budget_vs_spent' => $budgets->map(function ($budget) {
                return [
                    'category' => $budget->category->name ?? 'Uncategorized',
                    'budget' => $budget->amount,
                    'spent' => $budget->spent_amount
                ];
            }),
            'category_breakdown' => $budgets->groupBy('category.name')->map->sum('amount')
        ];
    }

    /**
     * Build custom report based on configuration
     *
     * @param array $config The report configuration
     *
     * @return array
     */
    private function _buildCustomReport($config)
    {
        $dataSources = $config['data_sources'] ?? [];
        $dateRange = [
            Carbon::parse($config['start_date']),
            Carbon::parse($config['end_date'])
        ];
        
        $data = [];
        
        foreach ($dataSources as $source) {
            switch ($source) {
                case 'orders':
                    $data['orders'] = Order::whereBetween('created_at', $dateRange)->get();
                    break;
                case 'expenses':
                    $data['expenses'] = Expense::whereBetween('created_at', $dateRange)->get();
                    break;
                case 'visits':
                    $data['visits'] = Visit::whereBetween('created_at', $dateRange)->get();
                    break;
                case 'budgets':
                    $data['budgets'] = Budget::whereBetween('created_at', $dateRange)->get();
                    break;
            }
        }
        
        return $data;
    }

    /**
     * Prepare chart data for custom reports
     *
     * @param array $data   The report data
     * @param array $config The chart configuration
     *
     * @return array
     */
    private function _prepareChartData($data, $config)
    {
        $chartType = $config['chart_type'] ?? 'line';
        $groupBy = $config['group_by'] ?? 'day';
        
        $chartData = [];
        
        foreach ($data as $key => $collection) {
            if ($collection instanceof \Illuminate\Database\Eloquent\Collection) {
                $chartData[$key] = $this->_groupDataForChart($collection, $groupBy);
            }
        }
        
        return $chartData;
    }

    /**
     * Group data for chart visualization
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection The data collection
     * @param string                                    $groupBy    The grouping criteria
     *
     * @return array
     */
    private function _groupDataForChart($collection, $groupBy)
    {
        switch ($groupBy) {
            case 'day':
                return $collection->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                })->map->count();
            case 'week':
                return $collection->groupBy(function ($item) {
                    return $item->created_at->format('Y-W');
                })->map->count();
            case 'month':
                return $collection->groupBy(function ($item) {
                    return $item->created_at->format('Y-m');
                })->map->count();
            default:
                return $collection->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                })->map->count();
        }
    }

    /**
     * Get export data based on type and date range
     *
     * @param string                   $type      The export type
     * @param string                   $dateRange The date range
     * @param \Illuminate\Http\Request $request   The HTTP request object
     *
     * @return array
     */
    private function _getExportData($type, $dateRange, Request $request)
    {
        $dates = $this->_parseDateRange($dateRange);
        $userIds = $this->_getAccessibleUserIds();
        
        switch ($type) {
            case 'sales':
                return Order::whereBetween('created_at', $dates)
                    ->whereIn('user_id', $userIds)
                    ->get();
            case 'expenses':
                return Expense::whereBetween('created_at', $dates)
                    ->whereIn('user_id', $userIds)
                    ->get();
            case 'visits':
                return Visit::whereBetween('created_at', $dates)
                    ->whereIn('user_id', $userIds)
                    ->get();
            case 'budgets':
                return Budget::whereBetween('created_at', $dates)
                    ->whereIn('user_id', $userIds)
                    ->with(['category'])
                    ->get();
            default:
                return collect([]);
        }
    }

    /**
     * Parse date range string into Carbon instances
     *
     * @param string $dateRange The date range string
     *
     * @return array
     */
    private function _parseDateRange($dateRange)
    {
        switch ($dateRange) {
            case 'today':
                return [Carbon::today(), Carbon::today()->endOfDay()];
            case 'yesterday':
                return [Carbon::yesterday(), Carbon::yesterday()->endOfDay()];
            case 'this_week':
                return [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
            case 'last_week':
                return [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ];
            case 'this_month':
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
            case 'last_month':
                return [
                    Carbon::now()->subMonth()->startOfMonth(),
                    Carbon::now()->subMonth()->endOfMonth()
                ];
            case 'this_year':
                return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()];
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
        }
    }

    /**
     * Export data to PDF format
     *
     * @param \Illuminate\Database\Eloquent\Collection $data The data to export
     * @param string                                    $type The export type
     *
     * @return \Illuminate\Http\Response
     */
    private function _exportToPdf($data, $type)
    {
        $html = $this->_generateReportHtml($data, $type);
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($html);
        
        return $pdf->download("{$type}_report_" . date('Y-m-d') . '.pdf');
    }

    /**
     * Export data to Excel format
     *
     * @param \Illuminate\Database\Eloquent\Collection $data The data to export
     * @param string                                    $type The export type
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    private function _exportToExcel($data, $type)
    {
        $exportData = $this->_prepareExcelData($data, $type);
        
        return Excel::download(
            new class($exportData) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $data;
                
                public function __construct($data)
                {
                    $this->data = $data;
                }
                
                public function array(): array
                {
                    return $this->data;
                }
            },
            "{$type}_report_" . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export data to CSV format
     *
     * @param \Illuminate\Database\Eloquent\Collection $data The data to export
     * @param string                                    $type The export type
     *
     * @return \Illuminate\Http\Response
     */
    private function _exportToCsv($data, $type)
    {
        $csvData = $this->_prepareCsvData($data, $type);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $type . '_report_' . date('Y-m-d') . '.csv"',
        ];
        
        return response($csvData, 200, $headers);
    }

    /**
     * Generate HTML for PDF reports
     *
     * @param \Illuminate\Database\Eloquent\Collection $data The data to export
     * @param string                                    $type The report type
     *
     * @return string
     */
    private function _generateReportHtml($data, $type)
    {
        $html = '<html><head><title>' . ucfirst($type) . ' Report</title></head><body>';
        $html .= '<h1>' . ucfirst($type) . ' Report</h1>';
        $html .= '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';
        
        $html .= '<table border="1" style="width:100%; border-collapse: collapse;">';
        $html .= $this->_getReportHeaders($type);
        
        foreach ($data as $item) {
            $html .= $this->_getReportRow($item, $type);
        }
        
        $html .= '</table></body></html>';
        
        return $html;
    }

    /**
     * Get report headers based on type
     *
     * @param string $type The report type
     *
     * @return string
     */
    private function _getReportHeaders($type)
    {
        switch ($type) {
            case 'sales':
                return '<tr><th>Order ID</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr>';
            case 'expenses':
                return '<tr><th>Expense ID</th><th>Description</th><th>Amount</th><th>Category</th><th>Date</th></tr>';
            case 'visits':
                return '<tr><th>Visit ID</th><th>Customer</th><th>Purpose</th><th>Status</th><th>Date</th></tr>';
            case 'budgets':
                return '<tr><th>Budget ID</th><th>Category</th><th>Amount</th><th>Spent</th><th>Remaining</th></tr>';
            default:
                return '<tr><th>ID</th><th>Description</th><th>Date</th></tr>';
        }
    }

    /**
     * Get report row based on type
     *
     * @param mixed  $item The data item
     * @param string $type The report type
     *
     * @return string
     */
    private function _getReportRow($item, $type)
    {
        switch ($type) {
            case 'sales':
                return '<tr><td>' . $item->id . '</td><td>' . ($item->customer_name ?? 'N/A') . '</td><td>$' . number_format($item->total_amount, 2) . '</td><td>' . $item->status . '</td><td>' . $item->created_at->format('Y-m-d') . '</td></tr>';
            case 'expenses':
                return '<tr><td>' . $item->id . '</td><td>' . $item->description . '</td><td>$' . number_format($item->amount, 2) . '</td><td>' . ($item->category ?? 'N/A') . '</td><td>' . $item->created_at->format('Y-m-d') . '</td></tr>';
            case 'visits':
                return '<tr><td>' . $item->id . '</td><td>' . ($item->customer_name ?? 'N/A') . '</td><td>' . $item->purpose . '</td><td>' . $item->status . '</td><td>' . $item->created_at->format('Y-m-d') . '</td></tr>';
            case 'budgets':
                return '<tr><td>' . $item->id . '</td><td>' . ($item->category->name ?? 'N/A') . '</td><td>$' . number_format($item->amount, 2) . '</td><td>$' . number_format($item->spent_amount, 2) . '</td><td>$' . number_format($item->amount - $item->spent_amount, 2) . '</td></tr>';
            default:
                return '<tr><td>' . $item->id . '</td><td>N/A</td><td>' . $item->created_at->format('Y-m-d') . '</td></tr>';
        }
    }

    /**
     * Prepare data for Excel export
     *
     * @param \Illuminate\Database\Eloquent\Collection $data The data to export
     * @param string                                    $type The export type
     *
     * @return array
     */
    private function _prepareExcelData($data, $type)
    {
        $exportData = [];
        
        // Add headers
        switch ($type) {
            case 'sales':
                $exportData[] = ['Order ID', 'Customer', 'Amount', 'Status', 'Date'];
                break;
            case 'expenses':
                $exportData[] = ['Expense ID', 'Description', 'Amount', 'Category', 'Date'];
                break;
            case 'visits':
                $exportData[] = ['Visit ID', 'Customer', 'Purpose', 'Status', 'Date'];
                break;
            case 'budgets':
                $exportData[] = ['Budget ID', 'Category', 'Amount', 'Spent', 'Remaining'];
                break;
        }
        
        // Add data rows
        foreach ($data as $item) {
            switch ($type) {
                case 'sales':
                    $exportData[] = [
                        $item->id,
                        $item->customer_name ?? 'N/A',
                        $item->total_amount,
                        $item->status,
                        $item->created_at->format('Y-m-d')
                    ];
                    break;
                case 'expenses':
                    $exportData[] = [
                        $item->id,
                        $item->description,
                        $item->amount,
                        $item->category ?? 'N/A',
                        $item->created_at->format('Y-m-d')
                    ];
                    break;
                case 'visits':
                    $exportData[] = [
                        $item->id,
                        $item->customer_name ?? 'N/A',
                        $item->purpose,
                        $item->status,
                        $item->created_at->format('Y-m-d')
                    ];
                    break;
                case 'budgets':
                    $exportData[] = [
                        $item->id,
                        $item->category->name ?? 'N/A',
                        $item->amount,
                        $item->spent_amount,
                        $item->amount - $item->spent_amount
                    ];
                    break;
            }
        }
        
        return $exportData;
    }

    /**
     * Prepare data for CSV export
     *
     * @param \Illuminate\Database\Eloquent\Collection $data The data to export
     * @param string                                    $type The export type
     *
     * @return string
     */
    private function _prepareCsvData($data, $type)
    {
        $csvData = '';
        
        // Add headers
        switch ($type) {
            case 'sales':
                $csvData .= "Order ID,Customer,Amount,Status,Date\n";
                break;
            case 'expenses':
                $csvData .= "Expense ID,Description,Amount,Category,Date\n";
                break;
            case 'visits':
                $csvData .= "Visit ID,Customer,Purpose,Status,Date\n";
                break;
            case 'budgets':
                $csvData .= "Budget ID,Category,Amount,Spent,Remaining\n";
                break;
        }
        
        // Add data rows
        foreach ($data as $item) {
            switch ($type) {
                case 'sales':
                    $csvData .= $item->id . ',"' . ($item->customer_name ?? 'N/A') . '",' . $item->total_amount . ',' . $item->status . ',' . $item->created_at->format('Y-m-d') . "\n";
                    break;
                case 'expenses':
                    $csvData .= $item->id . ',"' . $item->description . '",' . $item->amount . ',"' . ($item->category ?? 'N/A') . '",' . $item->created_at->format('Y-m-d') . "\n";
                    break;
                case 'visits':
                    $csvData .= $item->id . ',"' . ($item->customer_name ?? 'N/A') . '","' . $item->purpose . '",' . $item->status . ',' . $item->created_at->format('Y-m-d') . "\n";
                    break;
                case 'budgets':
                    $csvData .= $item->id . ',"' . ($item->category->name ?? 'N/A') . '",' . $item->amount . ',' . $item->spent_amount . ',' . ($item->amount - $item->spent_amount) . "\n";
                    break;
            }
        }
        
        return $csvData;
    }

    /**
     * Get recent activity data for dashboard
     *
     * @return array
     */
    private function _getRecentActivity()
    {
        $recentActivity = [];

        // Get recent orders
        $recentOrders = Order::latest()
            ->limit(5)
            ->get();

        foreach ($recentOrders as $order) {
            $recentActivity[] = [
                'type' => 'order',
                'title' => 'New Order #' . $order->id,
                'description' => 'Order from ' . ($order->customer_name ?? 'Unknown Customer'),
                'amount' => $order->total_amount,
                'date' => $order->created_at,
                'icon' => 'fas fa-shopping-cart',
                'color' => 'primary'
            ];
        }

        // Get recent expenses
        $recentExpenses = Expense::latest()
            ->limit(3)
            ->get();

        foreach ($recentExpenses as $expense) {
            $recentActivity[] = [
                'type' => 'expense',
                'title' => 'Expense: ' . $expense->description,
                'description' => 'Amount: $' . number_format($expense->amount, 2),
                'amount' => $expense->amount,
                'date' => $expense->created_at,
                'icon' => 'fas fa-receipt',
                'color' => 'warning'
            ];
        }

        // Get recent visits
        $recentVisits = Visit::latest()
            ->limit(3)
            ->get();

        foreach ($recentVisits as $visit) {
            $recentActivity[] = [
                'type' => 'visit',
                'title' => 'Visit Scheduled',
                'description' => 'Visit to ' . ($visit->customer_name ?? 'Unknown Customer'),
                'amount' => null,
                'date' => $visit->created_at,
                'icon' => 'fas fa-calendar-check',
                'color' => 'info'
            ];
        }

        // Sort by creation time and limit to 10 items
        usort(
            $recentActivity,
            function ($a, $b) {
                return $b['date']->timestamp - $a['date']->timestamp;
            }
        );

        return array_slice($recentActivity, 0, 10);
    }
}