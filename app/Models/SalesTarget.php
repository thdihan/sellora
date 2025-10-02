<?php

/**
 * Sales Target Model
 *
 * This model handles sales target management with role-based
 * assignment and achievement calculation logic.
 *
 * @category Models
 * @package  App\Models
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * SalesTarget Model
 *
 * @category Models
 * @package  App\Models
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 *
 * @property int $id
 * @property int $assigned_by_user_id
 * @property int $assigned_to_user_id
 * @property int $target_year
 * @property float $week_1_target
 * @property float $week_2_target
 * @property float $week_3_target
 * @property float $week_4_target
 * @property float $january_target
 * @property float $february_target
 * @property float $march_target
 * @property float $april_target
 * @property float $may_target
 * @property float $june_target
 * @property float $july_target
 * @property float $august_target
 * @property float $september_target
 * @property float $october_target
 * @property float $november_target
 * @property float $december_target
 * @property float $total_yearly_target
 * @property string $status
 * @property string $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class SalesTarget extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assigned_by_user_id',
        'assigned_to_user_id',
        'target_year',
        'week_1_target',
        'week_2_target',
        'week_3_target',
        'week_4_target',
        'january_target',
        'february_target',
        'march_target',
        'april_target',
        'may_target',
        'june_target',
        'july_target',
        'august_target',
        'september_target',
        'october_target',
        'november_target',
        'december_target',
        'total_yearly_target',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_year' => 'integer',
        'week_1_target' => 'decimal:2',
        'week_2_target' => 'decimal:2',
        'week_3_target' => 'decimal:2',
        'week_4_target' => 'decimal:2',
        'january_target' => 'decimal:2',
        'february_target' => 'decimal:2',
        'march_target' => 'decimal:2',
        'april_target' => 'decimal:2',
        'may_target' => 'decimal:2',
        'june_target' => 'decimal:2',
        'july_target' => 'decimal:2',
        'august_target' => 'decimal:2',
        'september_target' => 'decimal:2',
        'october_target' => 'decimal:2',
        'november_target' => 'decimal:2',
        'december_target' => 'decimal:2',
        'total_yearly_target' => 'decimal:2',
    ];
    
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(
            function ($salesTarget) {
                // Skip if this is already an automatic update to prevent infinite loops
                if ($salesTarget->isDirty(['total_yearly_target', 'week_1_target', 'week_2_target', 'week_3_target', 'week_4_target'])) {
                    return;
                }
                
                // Automatically recalculate yearly target when monthly targets change
                $monthlyTargets = [
                    $salesTarget->january_target ?? 0,
                    $salesTarget->february_target ?? 0,
                    $salesTarget->march_target ?? 0,
                    $salesTarget->april_target ?? 0,
                    $salesTarget->may_target ?? 0,
                    $salesTarget->june_target ?? 0,
                    $salesTarget->july_target ?? 0,
                    $salesTarget->august_target ?? 0,
                    $salesTarget->september_target ?? 0,
                    $salesTarget->october_target ?? 0,
                    $salesTarget->november_target ?? 0,
                    $salesTarget->december_target ?? 0,
                ];
                
                $totalYearlyTarget = array_sum($monthlyTargets);
                
                // Automatically redistribute weekly targets for current month
                $currentMonth = date('n');
                $weeklyTargets = self::calculateWeeklyTargets($monthlyTargets, $currentMonth);
                
                // Update both yearly and weekly targets without triggering another save event
                $salesTarget->updateQuietly(
                    array_merge(
                        $weeklyTargets,
                        ['total_yearly_target' => $totalYearlyTarget]
                    )
                );
                
                // Apply redistribution logic for unfilled targets
                self::redistributeWeeklyTargets($salesTarget->assigned_to_user_id);
                self::redistributeMonthlyTargets($salesTarget->assigned_to_user_id);
            }
        );
    }

    /**
     * Get the user who assigned this target.
     *
     * @return BelongsTo
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    /**
     * Get the user to whom this target is assigned.
     *
     * @return BelongsTo
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Get assignable employees based on role hierarchy.
     *
     * @param User $user The user assigning targets
     *
     * @return Collection
     */
    public static function getAssignableEmployees(User $user): Collection
    {
        $userRole = $user->role->name ?? $user->role;
        
        switch ($userRole) {
        case 'ASM':
            return User::whereHas(
                'role',
                function ($query) {
                    $query->whereIn('name', ['Trainee', 'MR', 'MPO']);
                }
            )->get();
        
        case 'RSM':
            return User::whereHas(
                'role',
                function ($query) {
                    $query->where('name', 'ASM');
                }
            )->get();
        
        case 'ZSM':
            return User::whereHas(
                'role',
                function ($query) {
                    $query->where('name', 'RSM');
                }
            )->get();
        
        case 'NSM':
            return User::whereHas(
                'role',
                function ($query) {
                    $query->where('name', 'ZSM');
                }
            )->get();
        
        case 'AGM':
        case 'DGM':
        case 'GM':
        case 'Director':
            return User::whereHas(
                'role',
                function ($query) {
                    $query->where('name', 'NSM');
                }
            )->get();
        
        case 'Chairman':
        case 'Admin':
        case 'Author':
            return User::whereHas(
                'role',
                function ($query) {
                    $query->whereIn(
                        'name',
                        ['AGM', 'DGM', 'GM', 'Director', 'NSM', 'ZSM', 'RSM', 'ASM', 'MR', 'MPO', 'Trainee']
                    );
                }
            )->get();
        
        default:
            return collect();
        }
    }

    /**
     * Get assignable employees grouped by hierarchy for dropdown display.
     *
     * @param User $user The user assigning targets
     *
     * @return array
     */
    public static function getAssignableEmployeesGrouped(User $user): array
    {
        $assignableEmployees = self::getAssignableEmployees($user);
        $grouped = [];
        
        // Define role hierarchy order
        $roleHierarchy = [
            'Chairman' => 'Chairman',
            'Director' => 'Director', 
            'GM' => 'General Manager',
            'DGM' => 'Deputy General Manager',
            'AGM' => 'Assistant General Manager',
            'NSM' => 'National Sales Manager',
            'ZSM' => 'Zonal Sales Manager', 
            'RSM' => 'Regional Sales Manager',
            'ASM' => 'Area Sales Manager',
            'MR' => 'Medical Representative',
            'MPO' => 'Medical Promotion Officer',
            'Trainee' => 'Trainee'
        ];
        
        foreach ($roleHierarchy as $roleKey => $roleLabel) {
            $roleEmployees = $assignableEmployees->filter(
                function ($employee) use ($roleKey) {
                    return $employee->role && $employee->role->name === $roleKey;
                }
            );
            
            if ($roleEmployees->count() > 0) {
                $grouped[$roleLabel] = $roleEmployees;
            }
        }
        
        return $grouped;
    }

    /**
     * Calculate achievement percentage for a user.
     *
     * @param User   $user        The user to calculate achievement for
     * @param string $period      The period (weekly, monthly, yearly)
     * @param mixed  $periodValue The specific period value
     *
     * @return float
     */
    public static function calculateAchievement(User $user, string $period = 'monthly', $periodValue = null): float
    {
        $target = self::where('assigned_to_user_id', $user->id)
            ->where('target_year', date('Y'))
            ->first();
        
        if (!$target) {
            return 0;
        }
        
        $targetAmount = self::_getTargetAmount($target, $period, $periodValue);
        $actualSales = self::_getActualSales($user, $period, $periodValue);
        
        return $targetAmount > 0 ? ($actualSales / $targetAmount) * 100 : 0;
    }

    /**
     * Get target amount for specific period.
     *
     * @param SalesTarget $target      The target record
     * @param string      $period      The period type
     * @param mixed       $periodValue The period value
     *
     * @return float
     */
    private static function _getTargetAmount(SalesTarget $target, string $period, $periodValue): float
    {
        switch ($period) {
        case 'weekly':
            return $target->{'week_' . $periodValue . '_target'} ?? 0;
        
        case 'monthly':
            $months = [
                1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april',
                5 => 'may', 6 => 'june', 7 => 'july', 8 => 'august',
                9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december'
            ];
            return $target->{$months[$periodValue] . '_target'} ?? 0;
        
        case 'yearly':
            return $target->total_yearly_target;
        
        default:
            return 0;
        }
    }

    /**
     * Get actual sales for a user based on role hierarchy.
     *
     * @param User   $user        The user to get sales for
     * @param string $period      The period type
     * @param mixed  $periodValue The period value
     *
     * @return float
     */
    private static function _getActualSales(User $user, string $period, $periodValue): float
    {
        $personalSales = self::_getPersonalSales($user, $period, $periodValue);
        $teamSales = self::_getTeamSales($user, $period, $periodValue);
        
        return $personalSales + $teamSales;
    }

    /**
     * Get personal sales for a user.
     *
     * @param User   $user        The user
     * @param string $period      The period type
     * @param mixed  $periodValue The period value
     *
     * @return float
     */
    private static function _getPersonalSales(User $user, string $period, $periodValue): float
    {
        $query = Order::where('user_id', $user->id)
            ->whereIn('status', [Order::STATUS_APPROVED, Order::STATUS_COMPLETED]);
        
        // Apply date filters based on period
        $query = self::_applyDateFilter($query, $period, $periodValue);
        
        return $query->sum('total_amount') ?? 0;
    }

    /**
     * Get team sales for a user based on role hierarchy.
     *
     * @param User   $user        The user
     * @param string $period      The period type
     * @param mixed  $periodValue The period value
     *
     * @return float
     */
    private static function _getTeamSales(User $user, string $period, $periodValue): float
    {
        $teamMembers = self::_getTeamMembers($user);
        $totalTeamSales = 0;
        
        foreach ($teamMembers as $member) {
            $totalTeamSales += self::_getPersonalSales($member, $period, $periodValue);
        }
        
        return $totalTeamSales;
    }

    /**
     * Get team members based on role hierarchy.
     *
     * @param User $user The user
     *
     * @return Collection
     */
    private static function _getTeamMembers(User $user): Collection
    {
        $userRole = $user->role->name ?? $user->role;
        
        switch ($userRole) {
        case 'ASM':
            return User::whereHas(
                'role',
                function ($query) {
                    $query->whereIn('name', ['Trainee', 'MR', 'MPO']);
                }
            )->get();
        
        case 'RSM':
            $asmUsers = User::whereHas(
                'role',
                function ($query) {
                    $query->where('name', 'ASM');
                }
            )->get();
            
            $teamMembers = collect();
            foreach ($asmUsers as $asm) {
                $teamMembers = $teamMembers->merge(self::_getTeamMembers($asm));
            }
            return $teamMembers->merge($asmUsers);
        
        case 'ZSM':
            $rsmUsers = User::whereHas(
                'role',
                function ($query) {
                    $query->where('name', 'RSM');
                }
            )->get();
            
            $teamMembers = collect();
            foreach ($rsmUsers as $rsm) {
                $teamMembers = $teamMembers->merge(self::_getTeamMembers($rsm));
            }
            return $teamMembers->merge($rsmUsers);
        
        case 'NSM':
            $zsmUsers = User::whereHas(
                'role',
                function ($query) {
                    $query->where('name', 'ZSM');
                }
            )->get();
            
            $teamMembers = collect();
            foreach ($zsmUsers as $zsm) {
                $teamMembers = $teamMembers->merge(self::_getTeamMembers($zsm));
            }
            return $teamMembers->merge($zsmUsers);
        
        case 'AGM':
        case 'DGM':
        case 'GM':
        case 'Director':
            $nsmUsers = User::where('role', 'NSM')
                ->where('team_id', $user->team_id)
                ->get();
            
            $teamMembers = collect();
            foreach ($nsmUsers as $nsm) {
                $teamMembers = $teamMembers->merge(self::_getTeamMembers($nsm));
            }
            return $teamMembers->merge($nsmUsers);
        
        default:
            return collect();
        }
    }

    /**
     * Apply date filter to query based on period.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query       The query builder
     * @param string                                $period      The period type
     * @param mixed                                 $periodValue The period value
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private static function _applyDateFilter($query, string $period, $periodValue)
    {
        $currentYear = date('Y');
        
        switch ($period) {
        case 'weekly':
            // Calculate week dates based on week number (1-4 of current month)
            $currentMonth = date('n');
            $weekStart = date('Y-m-d', strtotime("first day of {$currentYear}-{$currentMonth} +" . (($periodValue - 1) * 7) . " days"));
            $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));
            return $query->whereBetween('created_at', [$weekStart, $weekEnd]);
        
        case 'monthly':
            $monthStart = date('Y-m-01', strtotime("{$currentYear}-{$periodValue}-01"));
            $monthEnd = date('Y-m-t', strtotime("{$currentYear}-{$periodValue}-01"));
            return $query->whereBetween('created_at', [$monthStart, $monthEnd]);
        
        case 'yearly':
            $yearStart = "{$currentYear}-01-01";
            $yearEnd = "{$currentYear}-12-31";
            return $query->whereBetween('created_at', [$yearStart, $yearEnd]);
        
        default:
            return $query;
        }
    }

    /**
     * Get current week number (1-4) of the month.
     *
     * @return int
     */
    public static function getCurrentWeekOfMonth(): int
    {
        $dayOfMonth = date('j');
        return ceil($dayOfMonth / 7);
    }

    /**
     * Get achievement data for dashboard display.
     *
     * @param User $user The user
     *
     * @return array
     */
    public static function getAchievementData(User $user): array
    {
        $currentMonth = date('n');
        $currentWeek = self::getCurrentWeekOfMonth();
        $currentYear = date('Y');
        
        return [
            'weekly' => [
                'achievement' => self::calculateAchievement($user, 'weekly', $currentWeek),
                'target' => self::getTargetForPeriod($user, 'weekly', $currentWeek),
                'actual' => self::_getActualSales($user, 'weekly', $currentWeek)
            ],
            'monthly' => [
                'achievement' => self::calculateAchievement($user, 'monthly', $currentMonth),
                'target' => self::getTargetForPeriod($user, 'monthly', $currentMonth),
                'actual' => self::_getActualSales($user, 'monthly', $currentMonth)
            ],
            'yearly' => [
                'achievement' => self::calculateAchievement($user, 'yearly', $currentYear),
                'target' => self::getTargetForPeriod($user, 'yearly', $currentYear),
                'actual' => self::_getActualSales($user, 'yearly', $currentYear)
            ]
        ];
    }

    /**
     * Get target amount for a specific period and user.
     *
     * @param User   $user        The user
     * @param string $period      The period type
     * @param mixed  $periodValue The period value
     *
     * @return float
     */
    public static function getTargetForPeriod(User $user, string $period, $periodValue): float
    {
        $target = self::where('assigned_to_user_id', $user->id)
            ->where('target_year', date('Y'))
            ->first();
        
        if (!$target) {
            return 0;
        }
        
        return self::_getTargetAmount($target, $period, $periodValue);
    }

    /**
     * Calculate and distribute weekly targets from monthly targets.
     *
     * @param array    $monthlyTargets Array of monthly target values
     * @param int|null $targetMonth    Specific month to calculate for (1-12), null for current month
     *
     * @return array
     */
    public static function calculateWeeklyTargets(array $monthlyTargets, $targetMonth = null): array
    {
        $month = $targetMonth ?? date('n');
        $currentMonthTarget = $monthlyTargets[$month - 1] ?? 0;
        
        // Get current week of the month (limited to 4 weeks)
        $currentWeek = min(4, self::getCurrentWeekOfMonth());
        
        // If we're in the current month, distribute remaining target among remaining weeks
        if ($month == date('n')) {
            $remainingWeeks = max(1, 4 - $currentWeek + 1);
            $weeklyTarget = $currentMonthTarget > 0 ? $currentMonthTarget / $remainingWeeks : 0;
            
            $weeklyTargets = [
                'week_1_target' => 0,
                'week_2_target' => 0,
                'week_3_target' => 0,
                'week_4_target' => 0,
            ];
            
            // Distribute target only to current and future weeks (limited to 4 weeks)
            for ($week = $currentWeek; $week <= 4; $week++) {
                $weeklyTargets['week_' . $week . '_target'] = round($weeklyTarget, 2);
            }
            
            return $weeklyTargets;
        } else {
            // For future months, divide equally among 4 weeks
            $weeklyTarget = $currentMonthTarget > 0 ? $currentMonthTarget / 4 : 0;
            
            return [
                'week_1_target' => round($weeklyTarget, 2),
                'week_2_target' => round($weeklyTarget, 2),
                'week_3_target' => round($weeklyTarget, 2),
                'week_4_target' => round($weeklyTarget, 2),
            ];
        }
    }
    
    /**
     * Get number of weeks in a specific month.
     *
     * @param int $year  The year
     * @param int $month The month (1-12)
     *
     * @return int
     */
    public static function getWeeksInMonth(int $year, int $month): int
    {
        $firstDay = new \DateTime("$year-$month-01");
        $lastDay = new \DateTime("$year-$month-" . date('t', mktime(0, 0, 0, $month, 1, $year)));
        $daysInMonth = $lastDay->format('j');
        
        return (int) ceil($daysInMonth / 7);
    }
    


    /**
     * Redistribute unfilled weekly targets to next week.
     *
     * @param int                 $userId      The user ID
     * @param \Carbon\Carbon|null $currentDate Current date for calculations
     *
     * @return bool
     */
    public static function redistributeWeeklyTargets(int $userId, $currentDate = null): bool
    {
        $target = self::where('assigned_to_user_id', $userId)
            ->where('target_year', date('Y'))
            ->first();
        
        if (!$target) {
            return false;
        }
        
        $currentWeek = self::getCurrentWeekOfMonth();
        $currentMonth = date('n');
        
        // Get weekly target columns
        $weeklyColumns = [
            1 => 'week_1_target',
            2 => 'week_2_target', 
            3 => 'week_3_target',
            4 => 'week_4_target'
        ];
        
        // Check for unfilled targets from previous weeks and redistribute
        for ($week = 1; $week < $currentWeek; $week++) {
            $weekTarget = $target->{'week_' . $week . '_target'};
            $actualSales = self::_getActualSales(
                User::find($userId),
                'weekly',
                $week
            );
            
            $unfilled = $weekTarget - $actualSales;
            
            if ($unfilled > 0) {
                // Find next available week to redistribute unfilled amount
                for ($nextWeek = $week + 1; $nextWeek <= 4; $nextWeek++) {
                    $nextWeekColumn = $weeklyColumns[$nextWeek];
                    
                    // Add unfilled amount to next week's target
                    $currentNextWeekTarget = $target->{$nextWeekColumn};
                    $target->update(
                        [
                            $nextWeekColumn => $currentNextWeekTarget + $unfilled
                        ]
                    );
                    
                    // Reset current week's target to achieved amount
                    $target->update(
                        [
                            'week_' . $week . '_target' => $actualSales
                        ]
                    );
                    
                    break;
                }
            }
        }
        
        return true;
    }

    /**
     * Redistribute unachieved monthly targets to next month.
     *
     * @param int                 $userId      The user ID
     * @param \Carbon\Carbon|null $currentDate Current date for calculations
     *
     * @return bool
     */
    public static function redistributeMonthlyTargets(int $userId, $currentDate = null): bool
    {
        $target = self::where('assigned_to_user_id', $userId)
            ->where('target_year', date('Y'))
            ->first();
        
        if (!$target) {
            return false;
        }
        
        $currentMonth = date('n');
        $months = [
            1 => 'january', 2 => 'february', 3 => 'march', 4 => 'april',
            5 => 'may', 6 => 'june', 7 => 'july', 8 => 'august',
            9 => 'september', 10 => 'october', 11 => 'november', 12 => 'december'
        ];
        
        // Check for unachieved targets from previous months and redistribute
        for ($month = 1; $month < $currentMonth; $month++) {
            $monthKey = $months[$month];
            $monthTarget = $target->{$monthKey . '_target'};
            $actualSales = self::_getActualSales(
                User::find($userId),
                'monthly',
                $month
            );
            
            $unachieved = $monthTarget - $actualSales;
            
            if ($unachieved > 0) {
                // Find next available month to redistribute unachieved amount
                for ($nextMonth = $month + 1; $nextMonth <= 12; $nextMonth++) {
                    $nextMonthKey = $months[$nextMonth];
                    
                    // Add unachieved amount to next month's target
                    $currentNextMonthTarget = $target->{$nextMonthKey . '_target'};
                    $target->update(
                        [
                            $nextMonthKey . '_target' => $currentNextMonthTarget + $unachieved
                        ]
                    );
                    
                    // Reset current month's target to achieved amount
                    $target->update(
                        [
                            $monthKey . '_target' => $actualSales
                        ]
                    );
                    
                    // Recalculate weekly targets for the updated month if it's current month
                    if ($nextMonth == $currentMonth) {
                        $weeklyTargets = self::calculateWeeklyTargets(
                            [
                                $target->january_target, $target->february_target,
                                $target->march_target, $target->april_target,
                                $target->may_target, $target->june_target,
                                $target->july_target, $target->august_target,
                                $target->september_target, $target->october_target,
                                $target->november_target, $target->december_target
                            ],
                            $nextMonth
                        );
                        $target->update($weeklyTargets);
                    }
                    
                    break;
                }
            }
        }
        
        return true;
    }
}
