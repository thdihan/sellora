<?php

/**
 * LocationTrackingController
 *
 * Handles real-time location tracking API endpoints for pharma sales force.
 * Provides endpoints for storing and retrieving location data.
 *
 * @category Controller
 * @package  Sellora
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers;

use App\Models\LocationTracking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * LocationTrackingController
 *
 * Handles location tracking API endpoints
 *
 * @category Controller
 * @package  Sellora
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class LocationTrackingController extends Controller
{
    /**
     * Store a new location tracking record.
     *
     * POST /api/locations
     * Validates and stores user's current location with rate limiting.
     *
     * @param Request $request HTTP request with location data
     *
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Rate limiting: minimum 10 seconds between location posts
        $lastLocation = LocationTracking::where('user_id', Auth::id())
            ->where('captured_at', '>=', now()->subSeconds(10))
            ->first();

        if ($lastLocation) {
            return response()->json([
                'error' => 'Rate limit exceeded. Please wait at least 10 seconds between location updates.'
            ], 429);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 422);
        }

        try {
            // Store location
            $location = LocationTracking::create([
                'user_id' => Auth::id(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'captured_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location stored successfully',
                'data' => [
                    'id' => $location->id,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'accuracy' => $location->accuracy,
                    'captured_at' => $location->captured_at->toISOString(),
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to store location',
                'message' => 'An error occurred while saving your location.'
            ], 500);
        }
    }

    /**
     * Get latest location for each user (managers only).
     *
     * GET /api/locations/latest
     * Returns latest location per user with role-based access control.
     *
     * @param Request $request HTTP request
     *
     * @return JsonResponse
     */
    public function latest(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        // Role-based access control
        if ($user->role && $user->role->name === 'MR') {
            // MR can only see their own location
            $location = LocationTracking::with('user')
                ->where('user_id', $user->id)
                ->latest('captured_at')
                ->first();

            if (!$location) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [[
                    'id' => $location->user->id,
                    'name' => $location->user->name,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'accuracy' => $location->accuracy,
                    'captured_at' => $location->captured_at->toISOString(),
                    'time_ago' => $location->time_ago,
                    'is_recent' => $location->is_recent,
                ]]
            ]);
        }

        // ASM+ can see subordinates
        if (in_array($user->role, ['ASM', 'ZSM', 'NSM', 'Admin'])) {
            // Get user IDs based on hierarchy
            $userIds = $this->getSubordinateUserIds($user);
            
            $locations = LocationTracking::with('user')
                ->whereIn('user_id', $userIds)
                ->latestPerUser()
                ->orderBy('captured_at', 'desc')
                ->get();

            $data = $locations->map(function ($location) {
                return [
                    'id' => $location->user->id,
                    'name' => $location->user->name,
                    'role' => $location->user->role ?? 'MR',
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'accuracy' => $location->accuracy,
                    'captured_at' => $location->captured_at->toISOString(),
                    'time_ago' => $location->time_ago,
                    'is_recent' => $location->is_recent,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        return response()->json([
            'error' => 'Unauthorized access'
        ], 403);
    }

    /**
     * Get subordinate user IDs based on role hierarchy.
     *
     * @param User $user Current user
     *
     * @return array
     */
    private function getSubordinateUserIds(User $user): array
    {
        // Simple hierarchy implementation
        // In a real app, this would be more complex with proper territory/region mapping
        
        switch ($user->role) {
            case 'Author':
            case 'Admin':
                // Author and Admin can see everyone
                return User::pluck('id')->toArray();
                
            case 'NSM':
                // NSM can see ZSMs, ASMs, and MRs
                return User::whereIn('role', ['ZSM', 'ASM', 'MR'])
                    ->pluck('id')->toArray();
                    
            case 'ZSM':
                // ZSM can see ASMs and MRs in their zone
                return User::whereIn('role', ['ASM', 'MR'])
                    ->pluck('id')->toArray();
                    
            case 'ASM':
                // ASM can see MRs in their area
                return User::where('role', 'MR')
                    ->pluck('id')->toArray();
                    
            default:
                return [$user->id];
        }
    }

    /**
     * Get user's own location history.
     *
     * GET /api/locations/history
     *
     * @param Request $request HTTP request
     *
     * @return JsonResponse
     */
    public function history(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 50), 100); // Max 100 records
        $days = min($request->get('days', 7), 30); // Max 30 days

        $locations = LocationTracking::where('user_id', Auth::id())
            ->where('captured_at', '>=', now()->subDays($days))
            ->orderBy('captured_at', 'desc')
            ->limit($limit)
            ->get();

        $data = $locations->map(function ($location) {
            return [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'accuracy' => $location->accuracy,
                'captured_at' => $location->captured_at->toISOString(),
                'time_ago' => $location->time_ago,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'total' => $data->count(),
                'days' => $days,
                'limit' => $limit,
            ]
        ]);
    }
}