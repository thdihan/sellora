<?php

/**
 * Sales Target Controller
 *
 * This controller handles CRUD operations for sales targets
 * with role-based permissions and filtering.
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\SalesTarget;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * SalesTargetController Class
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class SalesTargetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get targets based on role permissions
        if ($user->role && in_array($user->role->name, ['Author', 'Admin'])) {
            // Author and Admin can see all targets
            $targets = SalesTarget::with(['assignedBy', 'assignedTo'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } else {
            // Other users can only see targets they're involved with
            $targets = SalesTarget::with(['assignedBy', 'assignedTo'])
                ->where('assigned_by_user_id', $user->id)
                ->orWhere('assigned_to_user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }
        
        return view('sales-targets.index', compact('targets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $user = Auth::user();
        $assignableEmployees = SalesTarget::getAssignableEmployees($user);
        $assignableEmployeesGrouped = SalesTarget::getAssignableEmployeesGrouped($user);
        
        return view('sales-targets.create', compact('assignableEmployees', 'assignableEmployeesGrouped'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request The HTTP request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'assigned_to_user_id' => 'required|exists:users,id',
                'target_year' => 'required|integer|min:2020|max:2030',
                'week_1_target' => 'nullable|numeric|min:0',
                'week_2_target' => 'nullable|numeric|min:0',
                'week_3_target' => 'nullable|numeric|min:0',
                'week_4_target' => 'nullable|numeric|min:0',
                'january_target' => 'nullable|numeric|min:0',
                'february_target' => 'nullable|numeric|min:0',
                'march_target' => 'nullable|numeric|min:0',
                'april_target' => 'nullable|numeric|min:0',
                'may_target' => 'nullable|numeric|min:0',
                'june_target' => 'nullable|numeric|min:0',
                'july_target' => 'nullable|numeric|min:0',
                'august_target' => 'nullable|numeric|min:0',
                'september_target' => 'nullable|numeric|min:0',
                'october_target' => 'nullable|numeric|min:0',
                'november_target' => 'nullable|numeric|min:0',
                'december_target' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
            ]
        );
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user = Auth::user();
        
        // Check if user can assign to this employee
        $assignableEmployees = SalesTarget::getAssignableEmployees($user);
        if (!$assignableEmployees->contains('id', $request->assigned_to_user_id)) {
            return redirect()->back()
                ->with('error', 'You are not authorized to assign targets to this employee.')
                ->withInput();
        }
        
        // Check for existing target for same user/year
        $existingTarget = SalesTarget::where('assigned_to_user_id', $request->assigned_to_user_id)
            ->where('target_year', $request->target_year)
            ->first();
        
        if ($existingTarget) {
            return redirect()->back()
                ->with('error', 'Target already exists for this employee and year.')
                ->withInput();
        }
        
        // Calculate total yearly target
        $monthlyTargets = [
            $request->january_target ?? 0,
            $request->february_target ?? 0,
            $request->march_target ?? 0,
            $request->april_target ?? 0,
            $request->may_target ?? 0,
            $request->june_target ?? 0,
            $request->july_target ?? 0,
            $request->august_target ?? 0,
            $request->september_target ?? 0,
            $request->october_target ?? 0,
            $request->november_target ?? 0,
            $request->december_target ?? 0,
        ];
        
        $totalYearlyTarget = array_sum($monthlyTargets);
        
        // Calculate weekly targets automatically from current month's target
        $currentMonth = date('n');
        $weeklyTargets = SalesTarget::calculateWeeklyTargets($monthlyTargets, $currentMonth);
        
        $salesTarget = SalesTarget::create(
            array_merge(
                $request->all(),
                [
                    'assigned_by_user_id' => $user->id,
                    'total_yearly_target' => $totalYearlyTarget,
                    'status' => 'active',
                ],
                $weeklyTargets
            )
        );
        
        return redirect()->route('sales-targets.index')
            ->with('success', 'Sales target created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param SalesTarget $salesTarget The sales target
     *
     * @return View
     */
    public function show(SalesTarget $salesTarget): View
    {
        $user = Auth::user();
        
        // Check if user can view this target
        if ($salesTarget->assigned_by_user_id !== $user->id
            && $salesTarget->assigned_to_user_id !== $user->id
        ) {
            abort(403, 'Unauthorized access to this target.');
        }
        
        $salesTarget->load(['assignedBy', 'assignedTo']);
        
        // Calculate current achievement
        $currentMonth = date('n');
        $achievement = SalesTarget::calculateAchievement(
            $salesTarget->assignedTo,
            'monthly',
            $currentMonth
        );
        
        return view('sales-targets.show', compact('salesTarget', 'achievement'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param SalesTarget $salesTarget The sales target
     *
     * @return View
     */
    public function edit(SalesTarget $salesTarget): View
    {
        $user = Auth::user();
        
        // Check if user can edit this target
        if ($salesTarget->assigned_by_user_id !== $user->id) {
            abort(403, 'Unauthorized to edit this target.');
        }
        
        $assignableEmployees = SalesTarget::getAssignableEmployees($user);
        $assignableEmployeesGrouped = SalesTarget::getAssignableEmployeesGrouped($user);
        
        return view('sales-targets.edit', compact('salesTarget', 'assignableEmployees', 'assignableEmployeesGrouped'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request     $request     The HTTP request
     * @param SalesTarget $salesTarget The sales target
     *
     * @return RedirectResponse
     */
    public function update(Request $request, SalesTarget $salesTarget): RedirectResponse
    {
        $user = Auth::user();
        
        // Check if user can update this target
        if ($salesTarget->assigned_by_user_id !== $user->id) {
            abort(403, 'Unauthorized to update this target.');
        }
        
        $validator = Validator::make(
            $request->all(),
            [
                'week_1_target' => 'nullable|numeric|min:0',
                'week_2_target' => 'nullable|numeric|min:0',
                'week_3_target' => 'nullable|numeric|min:0',
                'week_4_target' => 'nullable|numeric|min:0',
                'january_target' => 'nullable|numeric|min:0',
                'february_target' => 'nullable|numeric|min:0',
                'march_target' => 'nullable|numeric|min:0',
                'april_target' => 'nullable|numeric|min:0',
                'may_target' => 'nullable|numeric|min:0',
                'june_target' => 'nullable|numeric|min:0',
                'july_target' => 'nullable|numeric|min:0',
                'august_target' => 'nullable|numeric|min:0',
                'september_target' => 'nullable|numeric|min:0',
                'october_target' => 'nullable|numeric|min:0',
                'november_target' => 'nullable|numeric|min:0',
                'december_target' => 'nullable|numeric|min:0',
                'status' => 'required|in:active,inactive,completed',
                'notes' => 'nullable|string|max:1000',
            ]
        );
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Calculate total yearly target
        $monthlyTargets = [
            $request->january_target ?? 0,
            $request->february_target ?? 0,
            $request->march_target ?? 0,
            $request->april_target ?? 0,
            $request->may_target ?? 0,
            $request->june_target ?? 0,
            $request->july_target ?? 0,
            $request->august_target ?? 0,
            $request->september_target ?? 0,
            $request->october_target ?? 0,
            $request->november_target ?? 0,
            $request->december_target ?? 0,
        ];
        
        $totalYearlyTarget = array_sum($monthlyTargets);
        
        // Calculate weekly targets automatically from current month's target
        $currentMonth = date('n');
        $weeklyTargets = SalesTarget::calculateWeeklyTargets($monthlyTargets, $currentMonth);
        
        $salesTarget->update(
            array_merge(
                $request->all(),
                ['total_yearly_target' => $totalYearlyTarget],
                $weeklyTargets
            )
        );
        
        return redirect()->route('sales-targets.index')
            ->with('success', 'Sales target updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SalesTarget $salesTarget The sales target
     *
     * @return RedirectResponse
     */
    public function destroy(SalesTarget $salesTarget): RedirectResponse
    {
        $user = Auth::user();
        
        // Check if user can delete this target
        if ($salesTarget->assigned_by_user_id !== $user->id) {
            abort(403, 'Unauthorized to delete this target.');
        }
        
        $salesTarget->delete();
        
        return redirect()->route('sales-targets.index', request()->query())
            ->with('success', 'Sales target deleted successfully.');
    }

    /**
     * Get achievement data for dashboard.
     *
     * @param Request $request The HTTP request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAchievementData(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'monthly');
        $periodValue = $request->get('period_value', date('n'));
        
        $achievement = SalesTarget::calculateAchievement($user, $period, $periodValue);
        
        $target = SalesTarget::where('assigned_to_user_id', $user->id)
            ->where('target_year', date('Y'))
            ->first();
        
        return response()->json(
            [
                'achievement_percentage' => round($achievement, 2),
                'target' => $target,
                'period' => $period,
                'period_value' => $periodValue,
            ]
        );
    }

    /**
     * Show the bulk assignment form.
     *
     * @return View
     */
    public function bulkCreate(): View
    {
        return view('sales-targets.bulk-create');
    }

    /**
     * Bulk assign targets to all eligible employees.
     *
     * @param Request $request The request
     *
     * @return RedirectResponse
     */
    public function bulkStore(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'target_year' => 'required|integer|min:2020|max:2030',
                'january_target' => 'nullable|numeric|min:0',
                'february_target' => 'nullable|numeric|min:0',
                'march_target' => 'nullable|numeric|min:0',
                'april_target' => 'nullable|numeric|min:0',
                'may_target' => 'nullable|numeric|min:0',
                'june_target' => 'nullable|numeric|min:0',
                'july_target' => 'nullable|numeric|min:0',
                'august_target' => 'nullable|numeric|min:0',
                'september_target' => 'nullable|numeric|min:0',
                'october_target' => 'nullable|numeric|min:0',
                'november_target' => 'nullable|numeric|min:0',
                'december_target' => 'nullable|numeric|min:0',
                'status' => 'required|in:active,inactive,completed',
                'notes' => 'nullable|string|max:1000',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();
        $assignableEmployees = SalesTarget::getAssignableEmployees($user);
        
        if ($assignableEmployees->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No eligible employees found for target assignment.')
                ->withInput();
        }

        // Calculate monthly targets array
        $monthlyTargets = [
            $request->january_target ?? 0,
            $request->february_target ?? 0,
            $request->march_target ?? 0,
            $request->april_target ?? 0,
            $request->may_target ?? 0,
            $request->june_target ?? 0,
            $request->july_target ?? 0,
            $request->august_target ?? 0,
            $request->september_target ?? 0,
            $request->october_target ?? 0,
            $request->november_target ?? 0,
            $request->december_target ?? 0,
        ];
        
        $totalYearlyTarget = array_sum($monthlyTargets);
        
        // Calculate weekly targets automatically from current month's target
        $currentMonth = date('n');
        $weeklyTargets = SalesTarget::calculateWeeklyTargets($monthlyTargets, $currentMonth);
        
        $successCount = 0;
        $skippedCount = 0;
        $errors = [];
        
        foreach ($assignableEmployees as $employee) {
            // Check for existing target for same user/year
            $existingTarget = SalesTarget::where('assigned_to_user_id', $employee->id)
                ->where('target_year', $request->target_year)
                ->first();
            
            if ($existingTarget) {
                $skippedCount++;
                continue;
            }
            
            try {
                SalesTarget::create(
                    array_merge(
                        $request->all(),
                        [
                            'assigned_by_user_id' => $user->id,
                            'assigned_to_user_id' => $employee->id,
                            'total_yearly_target' => $totalYearlyTarget,
                            'status' => 'active',
                        ],
                        $weeklyTargets
                    )
                );
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to create target for {$employee->name}: {$e->getMessage()}";
            }
        }
        
        $message = "Bulk target assignment completed. Created: {$successCount}, Skipped: {$skippedCount}";
        
        if (!empty($errors)) {
            $message .= '. Errors: ' . implode(', ', $errors);
        }
        
        return redirect()->route('sales-targets.index')
            ->with('success', $message);
    }

    /**
     * Show the progress for a specific sales target.
     */
    public function progress(SalesTarget $target)
    {
        // Get actual sales data for the target
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        // Calculate progress based on orders/sales data
        $progressData = [
            'monthly_progress' => [],
            'weekly_progress' => [],
            'overall_progress' => 0,
            'current_achievement' => 0,
            'target_amount' => $target->monthly_targets[$currentMonth - 1] ?? 0,
        ];

        // You would implement actual progress calculation here based on your business logic
        // For now, returning a placeholder view
        return view('sales-targets.progress', compact('target', 'progressData'));
    }
}
