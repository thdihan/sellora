<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationVisit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Auth::user()->locations();
        
        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('favorites')) {
            $query->where('is_favorite', true);
        }
        
        // Apply sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if ($sortBy === 'distance' && $request->filled('lat') && $request->filled('lng')) {
            $query->nearby($request->lat, $request->lng);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }
        
        $locations = $query->paginate(12);
        
        // Get statistics
        $stats = [
            'total' => Auth::user()->locations()->count(),
            'favorites' => Auth::user()->locations()->favorites()->count(),
            'recent_visits' => Auth::user()->locationVisits()->recent(7)->count(),
            'active_locations' => Auth::user()->locations()->active()->count()
        ];
        
        return view('locations.index', compact('locations', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'type' => 'required|in:home,office,client,meeting,other',
            'notes' => 'nullable|string',
            'is_favorite' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        $location = Auth::user()->locations()->create(array_merge(
            $validator->validated(),
            ['status' => 'active']
        ));
        
        // Try to get address from coordinates if not provided
        if (!$request->filled('address')) {
            $location->updateAddressFromCoordinates();
        }
        
        return redirect()->route('locations.show', $location)
                        ->with('success', 'Location created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location): View
    {
        $this->authorize('view', $location);
        
        // Get recent visits
        $recentVisits = $location->visits()
                                ->with('user')
                                ->latest('visited_at')
                                ->limit(10)
                                ->get();
        
        // Get visit statistics
        $visitStats = [
            'total_visits' => $location->visits()->count(),
            'this_month' => $location->visits()->thisMonth()->count(),
            'this_week' => $location->visits()->thisWeek()->count(),
            'average_duration' => $location->visits()->completed()->avg('duration_minutes'),
            'last_visit' => $location->visits()->latest('visited_at')->first()
        ];
        
        // Check if user is currently checked in
        $currentVisit = $location->visits()
                               ->where('user_id', Auth::id())
                               ->current()
                               ->first();
        
        return view('locations.show', compact('location', 'recentVisits', 'visitStats', 'currentVisit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location): View
    {
        $this->authorize('update', $location);
        return view('locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location): RedirectResponse
    {
        $this->authorize('update', $location);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'type' => 'required|in:home,office,client,meeting,other',
            'status' => 'required|in:active,inactive,archived',
            'notes' => 'nullable|string',
            'is_favorite' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        $location->update($validator->validated());
        
        return redirect()->route('locations.show', $location)
                        ->with('success', 'Location updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location): RedirectResponse
    {
        $this->authorize('delete', $location);
        
        $location->delete();
        
        return redirect()->route('locations.index', request()->query())
                        ->with('success', 'Location deleted successfully!');
    }
    
    /**
     * Check in to a location
     */
    public function checkIn(Request $request, Location $location): JsonResponse
    {
        $this->authorize('view', $location);
        
        // Check if user is already checked in to this location
        $existingVisit = $location->visits()
                                 ->where('user_id', Auth::id())
                                 ->current()
                                 ->first();
        
        if ($existingVisit) {
            return response()->json([
                'success' => false,
                'message' => 'You are already checked in to this location.'
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $visit = LocationVisit::createVisit($location, array_merge(
            $validator->validated(),
            ['check_in_method' => $request->get('method', 'manual')]
        ));
        
        // Update location visit count
        $location->incrementVisitCount();
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully checked in!',
            'visit' => $visit
        ]);
    }
    
    /**
     * Check out from a location
     */
    public function checkOut(Request $request, Location $location): JsonResponse
    {
        $this->authorize('view', $location);
        
        $visit = $location->visits()
                         ->where('user_id', Auth::id())
                         ->current()
                         ->first();
        
        if (!$visit) {
            return response()->json([
                'success' => false,
                'message' => 'You are not currently checked in to this location.'
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
            'mood_rating' => 'nullable|integer|between:1,5',
            'productivity_rating' => 'nullable|integer|between:1,5'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Update visit with checkout data
        $visit->update(array_merge(
            $validator->validated(),
            ['check_out_method' => $request->get('method', 'manual')]
        ));
        
        // Perform checkout
        $visit->checkOut();
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully checked out!',
            'visit' => $visit->fresh()
        ]);
    }
    
    /**
     * Get nearby locations
     */
    public function nearby(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0.1|max:100'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $radius = $request->get('radius', 10); // Default 10km
        
        $locations = Auth::user()->locations()
                        ->active()
                        ->nearby($request->latitude, $request->longitude, $radius)
                        ->limit(20)
                        ->get();
        
        return response()->json([
            'success' => true,
            'locations' => $locations->map(function($location) {
                return $location->toGeoJson();
            })
        ]);
    }
    
    /**
     * Toggle favorite status
     */
    public function toggleFavorite(Location $location): JsonResponse
    {
        $this->authorize('update', $location);
        
        if ($location->is_favorite) {
            $location->unmarkAsFavorite();
            $message = 'Removed from favorites';
        } else {
            $location->markAsFavorite();
            $message = 'Added to favorites';
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorite' => $location->fresh()->is_favorite
        ]);
    }
    
    /**
     * Get location analytics
     */
    public function analytics(Location $location): View
    {
        $this->authorize('view', $location);
        
        // Visit trends (last 30 days)
        $visitTrends = $location->visits()
                              ->selectRaw('DATE(visited_at) as date, COUNT(*) as count')
                              ->where('visited_at', '>=', now()->subDays(30))
                              ->groupBy('date')
                              ->orderBy('date')
                              ->get();
        
        // Duration analysis
        $durationStats = $location->visits()
                               ->completed()
                               ->selectRaw('AVG(duration_minutes) as avg_duration, MIN(duration_minutes) as min_duration, MAX(duration_minutes) as max_duration')
                               ->first();
        
        // Purpose breakdown
        $purposeBreakdown = $location->visits()
                                  ->whereNotNull('purpose')
                                  ->selectRaw('purpose, COUNT(*) as count')
                                  ->groupBy('purpose')
                                  ->orderBy('count', 'desc')
                                  ->get();
        
        // Monthly statistics
        $monthlyStats = $location->visits()
                              ->selectRaw('YEAR(visited_at) as year, MONTH(visited_at) as month, COUNT(*) as visits, AVG(duration_minutes) as avg_duration')
                              ->groupBy('year', 'month')
                              ->orderBy('year', 'desc')
                              ->orderBy('month', 'desc')
                              ->limit(12)
                              ->get();
        
        return view('locations.analytics', compact(
            'location',
            'visitTrends',
            'durationStats',
            'purposeBreakdown',
            'monthlyStats'
        ));
    }
    
    /**
     * Export location data
     */
    public function export(Location $location): JsonResponse
    {
        $this->authorize('view', $location);
        
        $data = [
            'location' => $location->toArray(),
            'visits' => $location->visits()->with('user')->get()->toArray(),
            'exported_at' => now()->toISOString()
        ];
        
        return response()->json($data);
    }

    /**
     * Export all locations data
     */
    public function exportAll(Request $request): JsonResponse
    {
        $query = Auth::user()->locations();
        
        // Apply same filters as index
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('favorites')) {
            $query->where('is_favorite', true);
        }
        
        $locations = $query->with('visits.user')->get();
        
        $data = [
            'locations' => $locations->toArray(),
            'exported_at' => now()->toISOString(),
            'total_count' => $locations->count()
        ];
        
        return response()->json($data);
    }

    /**
     * Show location settings page
     */
    public function settings()
    {
        $apiKey = auth()->user()->api_token ?? 'sk_' . Str::random(32);
        return view('locations.settings', compact('apiKey'));
    }

    /**
     * Update location settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'auto_tracking' => 'boolean',
            'background_tracking' => 'boolean',
            'auto_checkin' => 'boolean',
            'auto_checkout' => 'boolean',
            'location_sharing' => 'boolean',
            'gps_accuracy' => 'in:high,medium,low',
            'update_frequency' => 'integer|min:5|max:300',
            'distance_threshold' => 'integer|min:1|max:1000',
            'geofence_radius' => 'integer|min:10|max:500',
            'checkin_notifications' => 'boolean',
            'checkout_notifications' => 'boolean',
            'new_location_notifications' => 'boolean',
            'daily_summary' => 'boolean',
            'weekly_report' => 'boolean',
            'data_retention' => 'integer|min:0|max:365',
            'anonymous_analytics' => 'boolean',
            'history_visibility' => 'in:private,team,public',
            'default_map_type' => 'in:roadmap,satellite,hybrid,terrain',
            'default_zoom' => 'integer|min:1|max:20',
            'show_traffic' => 'boolean',
            'show_transit' => 'boolean',
            'cluster_markers' => 'boolean',
            'google_calendar_integration' => 'boolean',
            'slack_integration' => 'boolean',
            'webhook_url' => 'nullable|url'
        ]);

        // Save settings to user preferences or settings table
        $user = auth()->user();
        $user->location_settings = json_encode($validated);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Show location dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_locations' => Location::where('user_id', auth()->id())->count(),
            'today_visits' => LocationVisit::where('user_id', auth()->id())
                ->whereDate('visited_at', today())
                ->count(),
            'current_location' => 'Office', // This would be determined by current check-in
            'total_time' => '8h 30m' // This would be calculated from today's visits
        ];

        return view('locations.dashboard', compact('stats'));
    }

    /**
     * Display team map view
     */
    public function teamMap(Request $request): View
    {
        // Get all team members with their latest locations
        $teamMembers = \App\Models\User::with(['locations' => function($query) {
            $query->orderByDesc('updated_at')->limit(1);
        }])->where('id', '!=', auth()->id())->get();

        // Get current user's locations for context
        $userLocations = auth()->user()->locations()->orderByDesc('updated_at')->get();

        return view('location.team-map', compact('teamMembers', 'userLocations'));
    }
}
