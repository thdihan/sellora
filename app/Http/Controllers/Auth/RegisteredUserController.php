<?php

/**
 * User registration controller
 * 
 * Handles user registration with automatic MR role assignment
 * 
 * @category Controllers
 * @package  App\Http\Controllers\Auth
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Class RegisteredUserController
 * 
 * Handles user registration operations
 * 
 * @category Controllers
 * @package  App\Http\Controllers\Auth
 * @author   Sellora Team <team@sellora.com>
 * @license  MIT License
 * @link     https://sellora.com
 */
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view
     *
     * @return View
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request
     *
     * @param Request $request The incoming request
     *
     * @return RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'employee_id' => ['nullable', 'string', 'max:50'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'designation' => ['nullable', 'string', 'max:255'],
            ]
        );

        // Auto-assign MR role (default role for new registrations)
        $defaultRole = Role::where('name', 'MR')->first();
        
        $user = User::create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'employee_id' => $request->employee_id,
                'password' => Hash::make($request->password),
                'role_id' => $defaultRole ? $defaultRole->id : 1, // Fallback to role ID 1 if MR not found
                'designation' => $request->designation,
            ]
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
