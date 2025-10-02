<?php

/**
 * Location Tracking Authentication Middleware
 *
 * Handles authentication for location tracking API endpoints.
 * Supports both Sanctum tokens and session-based authentication.
 *
 * @category Middleware
 * @package  Sellora\Http\Middleware
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Location Tracking Authentication Middleware
 */
class LocationTrackingAuth
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming request
     * @param Closure $next    The next middleware
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for Sanctum token authentication first
        if ($request->bearerToken()) {
            $user = $this->authenticateWithToken($request->bearerToken());
            if ($user) {
                Auth::setUser($user);
                return $next($request);
            }
        }
        
        // Check for session authentication
        if (Auth::check()) {
            return $next($request);
        }
        
        // Check for API token in user table (fallback)
        if ($request->header('Authorization')) {
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            $user = $this->authenticateWithApiToken($token);
            if ($user) {
                Auth::setUser($user);
                return $next($request);
            }
        }
        
        return response()->json([
            'error' => 'Unauthorized',
            'message' => 'Authentication required for location tracking'
        ], 401);
    }
    
    /**
     * Authenticate user with Sanctum token
     *
     * @param string $token The bearer token
     *
     * @return \App\Models\User|null
     */
    private function authenticateWithToken(string $token)
    {
        // If Sanctum is available, use it
        if (class_exists('Laravel\Sanctum\PersonalAccessToken')) {
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($accessToken && !$accessToken->tokenable->trashed()) {
                return $accessToken->tokenable;
            }
        }
        
        return null;
    }
    
    /**
     * Authenticate user with API token from users table
     *
     * @param string $token The API token
     *
     * @return \App\Models\User|null
     */
    private function authenticateWithApiToken(string $token)
    {
        if (empty($token)) {
            return null;
        }
        
        // Check if user has api_token column
        $user = \App\Models\User::where('api_token', $token)->first();
        
        if ($user && !$user->trashed()) {
            return $user;
        }
        
        return null;
    }
}