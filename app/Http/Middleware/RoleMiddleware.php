<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!$user->role) {
            abort(403, 'User has no assigned role.');
        }

        $userRole = $user->role->name;

        // Author role has unrestricted access to all routes
        if ($userRole === 'Author') {
            return $next($request);
        }

        // Check role permissions for other users
        if (!empty($roles) && !in_array($userRole, $roles)) {
            
            // Log access attempt for security monitoring
            \Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'required_roles' => $roles,
                'route' => $request->route()?->getName(),
                'url' => $request->url(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Insufficient permissions',
                    'message' => 'You do not have permission to access this resource.',
                    'required_roles' => $roles,
                    'your_role' => $userRole
                ], 403);
            }

            // Return 403 error page for regular requests
            abort(403, 'Insufficient permissions. Required roles: ' . implode(', ', $roles));
        }

        return $next($request);
    }
}
