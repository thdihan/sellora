<?php

/**
 * Event Controller File
 *
 * This file contains the EventController class for managing events.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Services\Mail\NotificationService;

/**
 * Event Controller
 *
 * Handles event management operations including CRUD operations,
 * calendar views, and role-based access control.
 *
 * @category Controller
 * @package  App\Http\Controllers
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class EventController extends Controller
{
    /**
     * Display a listing of events.
     *
     * @param Request $request The HTTP request instance
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $accessibleUserIds = $this->_getAccessibleUserIds();
        
        $query = Event::with('creator')
            ->whereIn('created_by', $accessibleUserIds)
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc');

        // Apply filters
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(
                function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                }
            );
        }

        $events = $query->paginate(12);

        // Get statistics
        $stats = [
            'total' => Event::whereIn('created_by', $accessibleUserIds)->count(),
            'upcoming' => Event::whereIn('created_by', $accessibleUserIds)
                ->upcoming()
                ->count(),
            'today' => Event::whereIn('created_by', $accessibleUserIds)
                ->today()
                ->count(),
            'completed' => Event::whereIn('created_by', $accessibleUserIds)
                ->byStatus('completed')
                ->count(),
        ];

        return view('events.index', compact('events', 'stats'));
    }

    /**
     * Display calendar view.
     *
     * @return View
     */
    public function calendar(): View
    {
        return view('events.calendar');
    }

    /**
     * Get events for calendar (AJAX).
     *
     * @param Request $request The HTTP request instance
     *
     * @return JsonResponse
     */
    public function getEvents(Request $request): JsonResponse
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $accessibleUserIds = $this->_getAccessibleUserIds();
        
        $events = Event::whereIn('created_by', $accessibleUserIds)
            ->dateRange($start, $end)
            ->get()
            ->map(
                function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'start' => $event->is_all_day 
                            ? $event->start_date->format('Y-m-d') 
                            : $event->start_time->format('Y-m-d\TH:i:s'),
                        'end' => $event->is_all_day 
                            ? $event->end_date->format('Y-m-d') 
                            : $event->end_time->format('Y-m-d\TH:i:s'),
                        'allDay' => $event->is_all_day,
                        'backgroundColor' => $event->getEventColor(),
                        'borderColor' => $event->getEventColor(),
                        'textColor' => '#ffffff',
                        'extendedProps' => [
                            'type' => $event->event_type,
                            'priority' => $event->priority,
                            'status' => $event->status,
                            'location' => $event->location,
                            'description' => $event->description,
                        ]
                    ];
                }
            );

        return response()->json($events);
    }

    /**
     * Show the form for creating a new event.
     *
     * @return View
     */
    public function create(): View
    {
        return view('events.create');
    }

    /**
     * Store a newly created event in storage.
     *
     * @param  Request  $request The HTTP request instance
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_type' => 'required|in:meeting,appointment,deadline,reminder,personal,holiday,other',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i|after:start_time',
                'location' => 'nullable|string|max:255',
                'is_all_day' => 'boolean',
                'priority' => 'required|in:low,medium,high,urgent',
                'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'reminder_minutes' => 'nullable|integer|min:0',
                'attendees' => 'nullable|array',
                'attendees.*' => 'email',
                'notes' => 'nullable|string',
                'recurring_type' => 'required|in:none,daily,weekly,monthly,yearly',
                'recurring_end_date' => 'nullable|date|after:start_date',
                'recurring_days' => 'nullable|array',
                'recurring_days.*' => 'integer|between:0,6',
                'attachments.*' => 'file|max:10240', // 10MB max
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['created_by'] = Auth::id();

        // Handle time fields
        if (!$data['is_all_day']) {
            $data['start_time'] = Carbon::parse(
                $data['start_date'] . ' ' . $data['start_time']
            );
            $data['end_time'] = Carbon::parse(
                $data['end_date'] . ' ' . $data['end_time']
            );
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('events/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
            $data['attachments'] = $attachments;
        }

        $event = Event::create($data);

        // Send notification for meeting creation
        if ($event->event_type === 'meeting' && !empty($event->attendees)) {
            $notificationService = new NotificationService();
            $notificationService->sendMeetingCreatedNotification($event);
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    /**
     * Display the specified event.
     *
     * @param Event $event The event instance
     *
     * @return View
     */
    public function show(Event $event): View
    {
        $this->authorize('view', $event);
        
        // Get related events (same type or similar date range)
        $relatedEvents = Event::where('id', '!=', $event->id)
            ->where(function ($query) use ($event) {
                $query->where('type', $event->type)
                    ->orWhereBetween('start_date', [
                        $event->start_date->subDays(7),
                        $event->start_date->addDays(7)
                    ]);
            })
            ->limit(5)
            ->get();
        
        return view('events.show', compact('event', 'relatedEvents'));
    }

    /**
     * Show the form for editing the specified event.
     *
     * @param Event $event The event instance
     *
     * @return View
     */
    public function edit(Event $event): View
    {
        $this->authorize('update', $event);
        
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     *
     * @param Request $request The HTTP request instance
     * @param Event $event The event instance
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $validator = Validator::make(
            $request->all(),
            [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_type' => 'required|in:meeting,appointment,deadline,reminder,personal,holiday,other',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i|after:start_time',
                'location' => 'nullable|string|max:255',
                'is_all_day' => 'boolean',
                'priority' => 'required|in:low,medium,high,urgent',
                'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
                'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'reminder_minutes' => 'nullable|integer|min:0',
                'attendees' => 'nullable|array',
                'attendees.*' => 'email',
                'notes' => 'nullable|string',
                'recurring_type' => 'required|in:none,daily,weekly,monthly,yearly',
                'recurring_end_date' => 'nullable|date|after:start_date',
                'recurring_days' => 'nullable|array',
                'recurring_days.*' => 'integer|between:0,6',
                'attachments.*' => 'file|max:10240',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle time fields
        if (!$data['is_all_day']) {
            $data['start_time'] = Carbon::parse(
                $data['start_date'] . ' ' . $data['start_time']
            );
            $data['end_time'] = Carbon::parse(
                $data['end_date'] . ' ' . $data['end_time']
            );
        } else {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

        // Handle new file attachments
        if ($request->hasFile('attachments')) {
            $existingAttachments = $event->attachments ?? [];
            $newAttachments = [];
            
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('events/attachments', 'public');
                $newAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
            
            $data['attachments'] = array_merge(
                $existingAttachments,
                $newAttachments
            );
        }

        $event->update($data);

        // Send notification for meeting updates if attendees exist
        if ($event->event_type === 'meeting' && !empty($event->attendees)) {
            $notificationService = new NotificationService();
            $notificationService->sendMeetingCreatedNotification($event);
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified event from storage.
     *
     * @param  Event  $event The event instance
     *
     * @return RedirectResponse
     */
    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        // Delete attachments from storage
        if ($event->attachments) {
            foreach ($event->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $event->delete();

        return redirect()->route('events.index', request()->query())
            ->with('success', 'Event deleted successfully!');
    }

    /**
     * Check user permissions for an event.
     *
     * @param Event $event The event instance
     *
     * @return JsonResponse
     */
    public function permissions(Event $event): JsonResponse
    {
        return response()->json([
            'canUpdate' => auth()->user()->can('update', $event),
            'canDelete' => auth()->user()->can('delete', $event),
        ]);
    }

    /**
     * Update event status (AJAX).
     *
     * @param  Request  $request The HTTP request instance
     * @param  Event    $event The event instance
     *
     * @return JsonResponse
     */
    public function updateStatus(Request $request, Event $event): JsonResponse
    {
        $this->authorize('update', $event);

        $request->validate([
             'status' => 'required|in:scheduled,in_progress,completed,cancelled,postponed',
         ]);

        $event->update(['status' => $request->status]);

        return response()->json([
             'success' => true,
             'message' => 'Event status updated successfully!',
             'status' => $event->status,
         ]);
    }

    /**
     * Remove attachment from event.
     *
     * @param  Request  $request The HTTP request instance
     * @param  Event    $event The event instance
     *
     * @return JsonResponse
     */
    public function removeAttachment(Request $request, Event $event): JsonResponse
    {
        $this->authorize('update', $event);

        $attachmentIndex = $request->input('index');
        $attachments = $event->attachments ?? [];

        if (isset($attachments[$attachmentIndex])) {
            // Delete file from storage
            Storage::disk('public')->delete($attachments[$attachmentIndex]['path']);
            
            // Remove from array
            unset($attachments[$attachmentIndex]);
            $attachments = array_values($attachments); // Re-index array
            
            $event->update(['attachments' => $attachments]);

            return response()->json([
                 'success' => true,
                 'message' => 'Attachment removed successfully!',
             ]);
        }

        return response()->json([
             'success' => false,
             'message' => 'Attachment not found!',
         ], 404);
    }

    /**
     * Download attachment.
     *
     * @param Event $event The event instance
     * @param int $index The attachment index
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAttachment(Event $event, int $index)
    {
        $this->authorize('view', $event);

        $attachments = $event->attachments ?? [];

        if (!isset($attachments[$index])) {
            abort(404, 'Attachment not found');
        }

        $attachment = $attachments[$index];
        $filePath = storage_path('app/public/' . $attachment['path']);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $attachment['name']);
    }

    /**
     * Get upcoming events for dashboard widget.
     *
     * @param  Request  $request The HTTP request instance
     *
     * @return JsonResponse
     */
    public function getUpcomingEvents(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 5);
        
        $accessibleUserIds = $this->_getAccessibleUserIds();
        
        $events = Event::whereIn('created_by', $accessibleUserIds)
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit($limit)
            ->get()
            ->map(
                function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'type' => $event->event_type,
                        'priority' => $event->priority,
                        'status' => $event->status,
                        'start_date' => $event->formatted_start_date,
                        'location' => $event->location,
                        'color' => $event->getEventColor(),
                        'url' => route('events.show', $event)
                    ];
                }
            );

        return response()->json($events);
    }

    /**
     * Display upcoming events.
     *
     * @param  Request  $request The HTTP request instance
     *
     * @return View
     */
    public function upcoming(Request $request): View
    {
        $accessibleUserIds = $this->_getAccessibleUserIds();
        
        $query = Event::with('creator')
            ->whereIn('created_by', $accessibleUserIds)
            ->upcoming()
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(
                function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                }
            );
        }

        $events = $query->paginate(12);

        // Get upcoming events statistics
        $stats = [
            'total_upcoming' => Event::whereIn('created_by', $accessibleUserIds)
                ->upcoming()
                ->count(),
            'today' => Event::whereIn('created_by', $accessibleUserIds)
                ->today()
                ->count(),
            'this_week' => Event::whereIn('created_by', $accessibleUserIds)
                ->upcoming()
                ->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month' => Event::whereIn('created_by', $accessibleUserIds)
                ->upcoming()
                ->whereBetween('start_date', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
        ];

        return view('events.upcoming', compact('events', 'stats'));
    }

    /**
     * Duplicate an event.
     *
     * @param  Event  $event The event instance
     *
     * @return RedirectResponse
     */
    public function duplicate(Event $event): RedirectResponse
    {
        $this->authorize('view', $event);

        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Copy)';
        $newEvent->status = 'scheduled';
        $newEvent->created_by = Auth::id();
        
        // Set new dates (1 week from original)
        $newEvent->start_date = $event->start_date->addWeek();
        $newEvent->end_date = $event->end_date->addWeek();
        
        if (!$event->is_all_day) {
            $newEvent->start_time = $event->start_time->addWeek();
            $newEvent->end_time = $event->end_time->addWeek();
        }
        
        $newEvent->save();

        return redirect()->route('events.edit', $newEvent)
            ->with('success', 'Event duplicated successfully! Please review and update the details.');
    }

    /**
     * Get accessible user IDs based on role hierarchy.
     *
     * @return array
     */
    private function _getAccessibleUserIds(): array
    {
        $user = Auth::user();
        
        if (!$user || !$user->role) {
            return [$user->id ?? 0];
        }
        
        $roleName = $user->role->name;
        
        switch ($roleName) {
            case 'Admin':
            case 'Author':
                // Admin can see all users' events
                return User::pluck('id')->toArray();
                
            case 'NSM':
                // NSM can see ZSMs, ASMs, and MRs events
                return User::whereHas(
                    'role',
                    function ($q) {
                        $q->whereIn('name', ['ZSM', 'ASM', 'MR']);
                    }
                )->pluck('id')->toArray();
                
            case 'ZSM':
                // ZSM can see ASMs and MRs events in their zone
                return User::whereHas(
                    'role',
                    function ($q) {
                        $q->whereIn('name', ['ASM', 'MR']);
                    }
                )->pluck('id')->toArray();
                
            case 'ASM':
                // ASM can see MRs events in their area
                return User::whereHas(
                    'role',
                    function ($q) {
                        $q->where('name', 'MR');
                    }
                )->pluck('id')->toArray();
                
            default:
                // Regular users can only see their own events
                return [$user->id];
        }
    }
}
