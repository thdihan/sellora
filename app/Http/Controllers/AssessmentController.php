<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Assessment::with('creator')
            ->withCount(['attempts', 'attempts as completed_attempts_count' => function ($query) {
                $query->where('status', 'completed');
            }]);

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->available();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $assessments = $query->latest()->paginate(10);

        // Get statistics
        $stats = [
            'total' => Assessment::count(),
            'active' => Assessment::active()->count(),
            'completed_attempts' => AssessmentAttempt::completed()->count(),
            'average_score' => AssessmentAttempt::completed()->avg('score') ?? 0
        ];

        return view('assessments.index', compact('assessments', 'stats'));
    }

    public function create()
    {
        return view('assessments.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'type' => 'required|in:quiz,survey,exam,self_assessment',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false,multiple_select,text',
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice,multiple_select|array',
            'questions.*.correct_answer' => 'required',
            'scoring_method' => 'required|in:percentage,points,weighted',
            'max_score' => 'required|numeric|min:1',
            'passing_score' => 'required|numeric|min:0|lte:max_score',
            'time_limit' => 'nullable|integer|min:1',
            'attempts_allowed' => 'nullable|integer|min:1',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'estimated_duration' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'instructions' => 'nullable|string',
            'tags' => 'nullable|array',
            'completion_message' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $assessment = Assessment::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'type' => $request->type,
            'questions' => $request->questions,
            'scoring_method' => $request->scoring_method,
            'max_score' => $request->max_score,
            'passing_score' => $request->passing_score,
            'time_limit' => $request->time_limit,
            'attempts_allowed' => $request->attempts_allowed,
            'is_active' => $request->boolean('is_active', true),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'instructions' => $request->instructions,
            'tags' => $request->tags,
            'difficulty_level' => $request->difficulty_level,
            'estimated_duration' => $request->estimated_duration,
            'auto_grade' => $request->boolean('auto_grade', true),
            'show_results_immediately' => $request->boolean('show_results_immediately', true),
            'randomize_questions' => $request->boolean('randomize_questions', false),
            'allow_review' => $request->boolean('allow_review', true),
            'completion_message' => $request->completion_message
        ]);

        return redirect()->route('assessments.show', $assessment)
            ->with('success', 'Assessment created successfully!');
    }

    public function show(Assessment $assessment)
    {
        $assessment->load('creator', 'attempts.user');
        
        $userAttempts = $assessment->attempts()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $analytics = $assessment->getAnalytics();
        
        $canAttempt = $assessment->canUserAttempt(Auth::id());
        $bestScore = $assessment->getUserBestScore(Auth::id());
        
        return view('assessments.show', compact(
            'assessment', 
            'userAttempts', 
            'analytics', 
            'canAttempt', 
            'bestScore'
        ));
    }

    public function edit(Assessment $assessment)
    {
        return view('assessments.edit', compact('assessment'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'type' => 'required|in:quiz,survey,exam,self_assessment',
            'questions' => 'required|array|min:1',
            'scoring_method' => 'required|in:percentage,points,weighted',
            'max_score' => 'required|numeric|min:1',
            'passing_score' => 'required|numeric|min:0|lte:max_score',
            'time_limit' => 'nullable|integer|min:1',
            'attempts_allowed' => 'nullable|integer|min:1',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'estimated_duration' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'instructions' => 'nullable|string',
            'tags' => 'nullable|array',
            'completion_message' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $assessment->update($request->only([
            'title', 'description', 'category', 'type', 'questions',
            'scoring_method', 'max_score', 'passing_score', 'time_limit',
            'attempts_allowed', 'start_date', 'end_date', 'instructions',
            'tags', 'difficulty_level', 'estimated_duration', 'completion_message'
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'auto_grade' => $request->boolean('auto_grade'),
            'show_results_immediately' => $request->boolean('show_results_immediately'),
            'randomize_questions' => $request->boolean('randomize_questions'),
            'allow_review' => $request->boolean('allow_review')
        ]);

        return redirect()->route('assessments.show', $assessment)
            ->with('success', 'Assessment updated successfully!');
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();
        
        return redirect()->route('assessments.index', request()->query())
            ->with('success', 'Assessment deleted successfully!');
    }

    public function take(Assessment $assessment)
    {
        if (!$assessment->is_available) {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'This assessment is not currently available.');
        }

        if (!$assessment->canUserAttempt(Auth::id())) {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'You have reached the maximum number of attempts for this assessment.');
        }

        // Check for existing in-progress attempt
        $existingAttempt = AssessmentAttempt::where('assessment_id', $assessment->id)
            ->where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->isTimeExpired()) {
                $existingAttempt->abandon();
            } else {
                return redirect()->route('assessments.attempt', [$assessment, $existingAttempt]);
            }
        }

        // Create new attempt
        $attempt = AssessmentAttempt::create([
            'assessment_id' => $assessment->id,
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $attempt->start();

        return redirect()->route('assessments.attempt', [$assessment, $attempt]);
    }

    public function attempt(Assessment $assessment, AssessmentAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$attempt->canContinue()) {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'This attempt has expired or is no longer available.');
        }

        $questions = $assessment->questions;
        if ($assessment->randomize_questions) {
            shuffle($questions);
        }

        return view('assessments.attempt', compact('assessment', 'attempt', 'questions'));
    }

    public function submit(Request $request, Assessment $assessment, AssessmentAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$attempt->canContinue()) {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'This attempt has expired.');
        }

        $answers = $request->input('answers', []);
        $results = $assessment->calculateScore($answers);
        
        $attempt->complete($answers, $results['score']);

        if ($assessment->show_results_immediately) {
            return redirect()->route('assessments.results', [$assessment, $attempt])
                ->with('success', 'Assessment completed successfully!');
        }

        return redirect()->route('assessments.show', $assessment)
            ->with('success', 'Assessment submitted successfully! Results will be available soon.');
    }

    public function results(Assessment $assessment, AssessmentAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$attempt->is_completed) {
            return redirect()->route('assessments.show', $assessment)
                ->with('error', 'This attempt is not completed yet.');
        }

        $results = $attempt->getResults();
        
        return view('assessments.results', compact('assessment', 'attempt', 'results'));
    }

    public function duplicate(Assessment $assessment)
    {
        $newAssessment = $assessment->duplicate();
        
        return redirect()->route('assessments.edit', $newAssessment)
            ->with('success', 'Assessment duplicated successfully! You can now modify it.');
    }

    public function analytics(Assessment $assessment)
    {
        $analytics = $assessment->getAnalytics();
        $attempts = $assessment->attempts()
            ->with('user')
            ->completed()
            ->latest()
            ->paginate(20);
            
        return view('assessments.analytics', compact('assessment', 'analytics', 'attempts'));
    }

    public function toggleStatus(Assessment $assessment)
    {
        $assessment->update([
            'is_active' => !$assessment->is_active
        ]);
        
        $status = $assessment->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Assessment {$status} successfully!");
    }

    public function export(Assessment $assessment)
    {
        $attempts = $assessment->attempts()
            ->with('user')
            ->completed()
            ->get();
            
        $csvData = [];
        $csvData[] = ['User', 'Email', 'Score', 'Percentage', 'Status', 'Duration (min)', 'Completed At'];
        
        foreach ($attempts as $attempt) {
            $csvData[] = [
                $attempt->user->name,
                $attempt->user->email,
                $attempt->score,
                $attempt->percentage_score . '%',
                $attempt->hasPassed() ? 'Passed' : 'Failed',
                $attempt->duration,
                $attempt->completed_at->format('Y-m-d H:i:s')
            ];
        }
        
        $filename = 'assessment_' . $assessment->id . '_results_' . now()->format('Y_m_d') . '.csv';
        
        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        exit;
    }
}
