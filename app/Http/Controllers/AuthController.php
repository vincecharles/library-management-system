<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        // Check if user exists
        if (!$user) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'No account found with that username.']);
        }

        // Check if user account is locked
        if ($user->status === 'locked') {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Your account has been locked due to multiple failed login attempts. Please contact an administrator.']);
        }

        // Check if user account is inactive
        if ($user->status === 'inactive') {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Your account is inactive. Please contact an administrator.']);
        }

        // Attempt authentication
        if (!Hash::check($request->password, $user->password)) {
            // Increment failed login attempts
            $failedAttempts = ($user->failed_login_attempts ?? 0) + 1;
            $user->failed_login_attempts = $failedAttempts;

            // Lock account after 3 failed attempts
            if ($failedAttempts >= 3) {
                $user->status = 'locked';
                $user->save();

                ActivityLog::create([
                    'user_id'    => $user->id,
                    'action'     => 'Account Locked',
                    'module'     => 'Authentication',
                    'details'    => "Account locked after {$failedAttempts} failed login attempts.",
                    'ip_address' => $request->ip(),
                ]);

                return back()
                    ->withInput($request->only('username'))
                    ->withErrors(['username' => 'Your account has been locked due to multiple failed login attempts. Please contact an administrator.']);
            }

            $user->save();

            return back()
                ->withInput($request->only('username'))
                ->withErrors(['password' => 'The password you entered is incorrect.']);
        }

        // Successful login - reset failed attempts
        $user->failed_login_attempts = 0;
        $user->last_login_at = now();
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        // Log the activity
        ActivityLog::create([
            'user_id'    => $user->id,
            'action'     => 'Login',
            'module'     => 'Authentication',
            'details'    => "User {$user->name} logged in successfully.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->intended(route('dashboard'))
            ->with('success', "Welcome back, {$user->name}!");
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLog::create([
                'user_id'    => $user->id,
                'action'     => 'Logout',
                'module'     => 'Authentication',
                'details'    => "User {$user->name} logged out.",
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }
}
