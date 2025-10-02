<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::with('user')
            ->when(Auth::user()->role && Auth::user()->role->name !== 'Admin', function ($q) {
                return $q->where('user_id', Auth::id());
            });

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('visit_type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        $visits = $query->orderBy('scheduled_at', 'desc')->paginate(15);

        $stats = [
            'total' => Visit::when(Auth::user()->role && Auth::user()->role->name !== 'Admin', fn($q) => $q->where('user_id', Auth::id()))->count(),
            'scheduled' => Visit::when(Auth::user()->role && Auth::user()->role->name !== 'Admin', fn($q) => $q->where('user_id', Auth::id()))->where('status', 'scheduled')->count(),
            'completed' => Visit::when(Auth::user()->role && Auth::user()->role->name !== 'Admin', fn($q) => $q->where('user_id', Auth::id()))->where('status', 'completed')->count(),
            'today' => Visit::when(Auth::user()->role && Auth::user()->role->name !== 'Admin', fn($q) => $q->where('user_id', Auth::id()))->whereDate('scheduled_at', today())->count(),
        ];

        return view('visits.index', compact('visits', 'stats'));
    }

    public function calendar(Request $request)
    {
        $start = $request->get('start', now()->startOfMonth());
        $end = $request->get('end', now()->endOfMonth());

        $visits = Visit::with('user')
            ->when(Auth::user()->role && Auth::user()->role->name !== 'Admin', function ($q) {
                return $q->where('user_id', Auth::id());
            })
            ->whereBetween('scheduled_at', [$start, $end])
            ->get()
            ->map(function ($visit) {
                return [
                    'id' => $visit->id,
                    'title' => $visit->customer_name . ' - ' . ucfirst($visit->visit_type),
                    'start' => $visit->scheduled_at->toISOString(),
                    'end' => $visit->scheduled_at->addHours($visit->estimated_duration)->toISOString(),
                    'backgroundColor' => $this->getEventColor($visit->status),
                    'borderColor' => $this->getEventColor($visit->status),
                    'extendedProps' => [
                        'status' => $visit->status,
                        'priority' => $visit->priority,
                        'customer_phone' => $visit->customer_phone,
                        'purpose' => $visit->purpose,
                    ]
                ];
            });

        if ($request->ajax()) {
            return response()->json($visits);
        }

        return view('visits.calendar', compact('visits'));
    }

    public function create()
    {
        return view('visits.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'required|string',
            'visit_type' => 'required|in:sales,support,delivery,consultation,follow_up',
            'purpose' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_duration' => 'nullable|numeric|min:0.1',
            'notes' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_address' => 'nullable|string',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        $validated['user_id'] = Auth::id();
        $validated['requires_follow_up'] = $request->has('requires_follow_up');

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('visit-attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ];
            }
            $validated['attachments'] = $attachments;
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'scheduled';
        
        Visit::create($validated);

        return redirect()->route('visits.index')
            ->with('success', 'Visit scheduled successfully!');
    }

    public function show(Visit $visit)
    {
        if (Auth::user()->role && Auth::user()->role->name !== 'Admin' && $visit->user_id !== Auth::id()) {
            abort(403);
        }

        return view('visits.show', compact('visit'));
    }

    public function edit(Visit $visit)
    {
        if (Auth::user()->role && Auth::user()->role->name !== 'Admin' && $visit->user_id !== Auth::id()) {
            abort(403);
        }

        return view('visits.edit', compact('visit'));
    }

    public function update(Request $request, Visit $visit)
    {
        if (Auth::user()->role && Auth::user()->role->name !== 'Admin' && $visit->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'required|string',
            'visit_type' => 'required|in:sales,support,delivery,consultation,follow_up',
            'purpose' => 'nullable|string',
            'scheduled_at' => 'required|date',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_duration' => 'nullable|numeric|min:0.1',
            'notes' => $visit->status === 'scheduled' ? 'required|string' : 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_address' => 'nullable|string',
        ]);

        $validated['requires_follow_up'] = $request->has('requires_follow_up');

        $visit->update($validated);

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Visit updated successfully!');
    }

    public function destroy(Visit $visit)
    {
        if (Auth::user()->role && Auth::user()->role->name !== 'Admin' && $visit->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete attachments
        if ($visit->attachments) {
            foreach ($visit->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $visit->delete();

        return redirect()->route('visits.index', request()->query())
            ->with('success', 'Visit deleted successfully!');
    }

    public function start(Visit $visit)
    {
        if (Auth::user()->role && Auth::user()->role->name !== 'Admin' && $visit->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visit->canBeStarted()) {
            return back()->with('error', 'This visit cannot be started at this time.');
        }

        $visit->update([
            'status' => 'in_progress',
            'actual_start_time' => now(),
        ]);

        return back()->with('success', 'Visit started successfully!');
    }

    public function complete(Request $request, Visit $visit)
    {
        if (Auth::user()->role && Auth::user()->role->name !== 'Admin' && $visit->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visit->canBeCompleted()) {
            return back()->with('error', 'Visit cannot be completed.');
        }

        $validated = $request->validate([
            'outcome' => 'required|string',
            'notes' => 'required|string',
            'requires_follow_up' => 'boolean',
            'follow_up_date' => 'nullable|date|after:today',
        ]);

        $endTime = now();
        $duration = $visit->actual_start_time ? 
            $visit->actual_start_time->diffInHours($endTime, true) : 
            $visit->estimated_duration;

        $visit->update([
            'status' => 'completed',
            'actual_end_time' => $endTime,
            'actual_duration' => $duration,
            'outcome' => $validated['outcome'],
            'notes' => $validated['notes'],
            'requires_follow_up' => $request->has('requires_follow_up'),
            'follow_up_date' => $validated['follow_up_date'] ?? null,
        ]);

        return back()->with('success', 'Visit completed successfully!');
    }

    public function reschedule(Request $request, Visit $visit)
    {
        if (Auth::user()->role && Auth::user()->role->name !== 'Admin' && $visit->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visit->canBeRescheduled()) {
            return back()->with('error', 'This visit cannot be rescheduled.');
        }

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'required|string',
        ]);

        $visit->update([
            'rescheduled_from' => $visit->scheduled_at,
            'scheduled_at' => $validated['scheduled_at'],
            'status' => 'scheduled',
            'notes' => $validated['notes'],
            'actual_start_time' => null,
            'actual_end_time' => null,
        ]);

        return back()->with('success', 'Visit rescheduled successfully!');
    }

    public function cancel(Request $request, Visit $visit)
    {
        if (Auth::user()->role && Auth::user()->role->name !== 'Admin' && $visit->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$visit->canBeCancelled()) {
            return back()->with('error', 'This visit cannot be cancelled.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
            'notes' => 'required|string',
        ]);

        $visit->update([
            'status' => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'],
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Visit cancelled successfully!');
    }

    private function getEventColor($status)
    {
        return match($status) {
            'scheduled' => '#007bff',
            'in_progress' => '#ffc107',
            'completed' => '#28a745',
            'cancelled' => '#dc3545',
            'rescheduled' => '#17a2b8',
            default => '#6c757d'
        };
    }
}
