<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

/**
 * Class UserController
 * 
 * Handles user management operations for the admin panel
 * 
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Display a listing of users
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $query = User::with('role');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(
                function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                }
            );
        }
        
        // Role filter
        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        $users = $query->paginate(15);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     *
     * @return View
     */
    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed'],
                'role_id' => ['required', 'exists:roles,id'],
                'designation' => ['nullable', 'string', 'max:255'],
            ]
        );

        $user = User::create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'designation' => $request->designation,
                'email_verified_at' => now(),
            ]
        );

        Log::info('User created by admin', [
            'created_user_id' => $user->id,
            'created_user_email' => $user->email,
            'admin_user_id' => auth()->id(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     *
     * @param User $user
     * @return View
     */
    public function show(User $user): View
    {
        $user->load('role');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param User $user
     * @return View|RedirectResponse
     */
    public function edit(User $user)
    {
        // Prevent editing the system owner unless allowed
        if ($user->isOwner() && !$user->canMutateOwner()) {
            return redirect()->route('users.index')
                ->with('error', 'System owner account cannot be modified.');
        }
        
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // Prevent updating the system owner unless allowed
        if ($user->isOwner() && !$user->canMutateOwner()) {
            return redirect()->route('users.index')
                ->with('error', 'System owner account cannot be modified.');
        }
        
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'designation' => ['nullable', 'string', 'max:255'],
        ];
        
        // Only validate password if provided
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed'];
        }
        
        $request->validate($rules);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'designation' => $request->designation,
        ];
        
        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        $user->update($updateData);

        Log::info('User updated by admin', [
            'updated_user_id' => $user->id,
            'updated_user_email' => $user->email,
            'admin_user_id' => auth()->id(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting the system owner unless allowed
        if ($user->isOwner() && !$user->canMutateOwner()) {
            return redirect()->route('users.index', request()->query())
                ->with('error', 'System owner account cannot be deleted.');
        }
        
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index', request()->query())
                ->with('error', 'You cannot delete your own account.');
        }

        Log::info('User deleted by admin', [
            'deleted_user_id' => $user->id,
            'deleted_user_email' => $user->email,
            'admin_user_id' => auth()->id(),
        ]);
        
        $user->delete();

        return redirect()->route('users.index', request()->query())
            ->with('success', 'User deleted successfully.');
    }
    
    /**
     * Bulk update user roles
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkUpdateRoles(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'user_ids' => ['required', 'array'],
                'user_ids.*' => ['exists:users,id'],
                'role_id' => ['required', 'exists:roles,id'],
            ]
        );
        
        $updatedCount = 0;
        $skippedOwners = 0;
        
        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);
            
            // Skip system owner unless allowed
            if ($user && $user->isOwner() && !$user->canMutateOwner()) {
                $skippedOwners++;
                continue;
            }
            
            if ($user) {
                $user->update(['role_id' => $request->role_id]);
                $updatedCount++;
            }
        }
        
        $message = "Updated {$updatedCount} user(s) successfully.";
        if ($skippedOwners > 0) {
            $message .= " Skipped {$skippedOwners} system owner account(s).";
        }
        
        Log::info('Bulk role update by admin', [
            'updated_count' => $updatedCount,
            'skipped_owners' => $skippedOwners,
            'new_role_id' => $request->role_id,
            'admin_user_id' => auth()->id(),
        ]);
        
        return redirect()->route('users.index')
            ->with('success', $message);
    }
}