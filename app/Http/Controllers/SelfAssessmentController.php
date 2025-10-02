<?php

/**
 * SelfAssessmentController
 *
 * Handles self-assessment operations for employees.
 * Provides CRUD operations and workflow management for assessments.
 *
 * @category Controller
 * @package  Sellora
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\SelfAssessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * SelfAssessmentController
 *
 * Manages employee self-assessment functionality
 *
 * @category Controller
 * @package  Sellora
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class SelfAssessmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of self assessments.
     *
     * @param Request $request HTTP request
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $query = SelfAssessment::with(['user', 'reviewer'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by period if provided
        if ($request->filled('period')) {
            $query->where('period', 'like', '%' . $request->period . '%');
        }

        $assessments = $query->paginate(10);
        $availablePeriods = SelfAssessment::getAvailablePeriods();

        return view('self-assessments.index', compact('assessments', 'availablePeriods'));
    }

    /**
     * Show the form for creating a new self assessment.
     *
     * @return View
     */
    public function create(): View
    {
        $availablePeriods = SelfAssessment::getAvailablePeriods();
        $usedPeriods = SelfAssessment::where('user_id', Auth::id())
            ->pluck('period')
            ->toArray();

        return view('self-assessments.create', compact('availablePeriods', 'usedPeriods'));
    }

    /**
     * Store a newly created self assessment.
     *
     * @param Request $request HTTP request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'period' => 'required|string|max:255',
            'targets' => 'required|string|min:10',
            'achievements' => 'required|string|min:10',
            'problems' => 'nullable|string',
            'solutions' => 'nullable|string',
            'market_analysis' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if user already has assessment for this period
        $existingAssessment = SelfAssessment::where('user_id', Auth::id())
            ->where('period', $request->period)
            ->first();

        if ($existingAssessment) {
            return redirect()->back()
                ->withErrors(['period' => 'You already have an assessment for this period.'])
                ->withInput();
        }

        $assessment = SelfAssessment::create([
            'user_id' => Auth::id(),
            'period' => $request->period,
            'targets' => $request->targets,
            'achievements' => $request->achievements,
            'problems' => $request->problems,
            'solutions' => $request->solutions,
            'market_analysis' => $request->market_analysis,
            'status' => 'draft'
        ]);

        return redirect()->route('self-assessments.show', $assessment)
            ->with('success', 'Self assessment created successfully!');
    }

    /**
     * Display the specified self assessment.
     *
     * @param SelfAssessment $selfAssessment The assessment to display
     *
     * @return View
     */
    public function show(SelfAssessment $selfAssessment): View
    {
        // Check if user can view this assessment
        if (!$selfAssessment->canBeViewedBy(Auth::id())) {
            abort(403, 'You do not have permission to view this assessment.');
        }

        return view('self-assessments.show', compact('selfAssessment'));
    }

    /**
     * Show the form for editing the specified self assessment.
     *
     * @param SelfAssessment $selfAssessment The assessment to edit
     *
     * @return View
     */
    public function edit(SelfAssessment $selfAssessment): View
    {
        // Check if user can edit this assessment
        if (!$selfAssessment->canBeEditedBy(Auth::id())) {
            abort(403, 'You cannot edit this assessment.');
        }

        $availablePeriods = SelfAssessment::getAvailablePeriods();
        $usedPeriods = SelfAssessment::where('user_id', Auth::id())
            ->where('id', '!=', $selfAssessment->id)
            ->pluck('period')
            ->toArray();

        return view('self-assessments.edit', compact('selfAssessment', 'availablePeriods', 'usedPeriods'));
    }

    /**
     * Update the specified self assessment.
     *
     * @param Request        $request        HTTP request
     * @param SelfAssessment $selfAssessment The assessment to update
     *
     * @return RedirectResponse
     */
    public function update(Request $request, SelfAssessment $selfAssessment): RedirectResponse
    {
        // Check if user can edit this assessment
        if (!$selfAssessment->canBeEditedBy(Auth::id())) {
            abort(403, 'You cannot edit this assessment.');
        }

        $validator = Validator::make($request->all(), [
            'period' => 'required|string|max:255',
            'targets' => 'required|string|min:10',
            'achievements' => 'required|string|min:10',
            'problems' => 'nullable|string',
            'solutions' => 'nullable|string',
            'market_analysis' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if period is already used by another assessment
        $existingAssessment = SelfAssessment::where('user_id', Auth::id())
            ->where('period', $request->period)
            ->where('id', '!=', $selfAssessment->id)
            ->first();

        if ($existingAssessment) {
            return redirect()->back()
                ->withErrors(['period' => 'You already have an assessment for this period.'])
                ->withInput();
        }

        $selfAssessment->update([
            'period' => $request->period,
            'targets' => $request->targets,
            'achievements' => $request->achievements,
            'problems' => $request->problems,
            'solutions' => $request->solutions,
            'market_analysis' => $request->market_analysis
        ]);

        return redirect()->route('self-assessments.show', $selfAssessment)
            ->with('success', 'Self assessment updated successfully!');
    }

    /**
     * Remove the specified self assessment.
     *
     * @param SelfAssessment $selfAssessment The assessment to delete
     *
     * @return RedirectResponse
     */
    public function destroy(SelfAssessment $selfAssessment): RedirectResponse
    {
        // Check if user can delete this assessment
        if (!$selfAssessment->canBeEditedBy(Auth::id())) {
            abort(403, 'You cannot delete this assessment.');
        }

        $selfAssessment->delete();

        return redirect()->route('self-assessments.index', request()->query())
            ->with('success', 'Self assessment deleted successfully!');
    }

    /**
     * Submit the assessment for review.
     *
     * @param SelfAssessment $selfAssessment The assessment to submit
     *
     * @return RedirectResponse
     */
    public function submit(SelfAssessment $selfAssessment): RedirectResponse
    {
        // Check if user can edit this assessment
        if (!$selfAssessment->canBeEditedBy(Auth::id())) {
            abort(403, 'You cannot submit this assessment.');
        }

        if ($selfAssessment->submit()) {
            return redirect()->route('self-assessments.show', $selfAssessment)
                ->with('success', 'Assessment submitted for review successfully!');
        }

        return redirect()->back()
            ->with('error', 'Failed to submit assessment.');
    }

    /**
     * Revert assessment back to draft.
     *
     * @param SelfAssessment $selfAssessment The assessment to revert
     *
     * @return RedirectResponse
     */
    public function revertToDraft(SelfAssessment $selfAssessment): RedirectResponse
    {
        // Only the assessment owner can revert to draft
        if ($selfAssessment->user_id !== Auth::id()) {
            abort(403, 'You cannot modify this assessment.');
        }

        if ($selfAssessment->revertToDraft()) {
            return redirect()->route('self-assessments.show', $selfAssessment)
                ->with('success', 'Assessment reverted to draft successfully!');
        }

        return redirect()->back()
            ->with('error', 'Failed to revert assessment to draft.');
    }

    /**
     * Mark assessment as reviewed (for managers)
     */
    public function markAsReviewed(Request $request, SelfAssessment $selfAssessment): RedirectResponse
    {
        // Check if user has permission to review assessments
        $user = Auth::user();
        $managerRoles = ['Author', 'Admin', 'Manager', 'ASM', 'RSM', 'ZSM', 'NSM', 'AGM', 'DGM', 'GM', 'ED', 'Director', 'Chairman'];
        
        if (!$user->role || !in_array($user->role->name, $managerRoles)) {
            abort(403, 'You do not have permission to review assessments.');
        }

        $validator = Validator::make($request->all(), [
            'reviewer_comments' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($selfAssessment->markAsReviewed(Auth::id(), $request->reviewer_comments)) {
            return redirect()->route('self-assessments.show', $selfAssessment)
                ->with('success', 'Assessment marked as reviewed successfully!');
        }

        return redirect()->back()
            ->with('error', 'Failed to mark assessment as reviewed.');
    }

    /**
     * Get assessment history timeline data
     */
    public function history(): View
    {
        $assessments = SelfAssessment::where('user_id', Auth::id())
            ->with(['reviewer'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($assessment) {
                return $assessment->created_at->format('Y');
            });

        return view('self-assessments.history', compact('assessments'));
    }
}