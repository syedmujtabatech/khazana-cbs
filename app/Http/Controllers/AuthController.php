<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SecurityAuditLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isCustomer() 
                ? redirect()->route('portal.dashboard') 
                : redirect()->route('cbs.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            SecurityAuditLog::create([
                'user_id' => $user->id,
                'event_type' => 'auth_login_success',
                'severity' => 'info',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "User {$user->name} ({$user->role}) authenticated successfully.",
            ]);

            if ($user->isCustomer()) {
                return redirect()->intended(route('portal.dashboard'));
            }
            return redirect()->intended(route('cbs.dashboard'));
        }

        SecurityAuditLog::create([
            'user_id' => null,
            'event_type' => 'auth_login_failed',
            'severity' => 'warning',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Failed login attempt for email: {$request->email}",
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our banking records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            SecurityAuditLog::create([
                'user_id' => Auth::id(),
                'event_type' => 'auth_logout',
                'severity' => 'info',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'description' => "User " . Auth::user()->name . " signed out.",
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Fast Role Switcher (For Evaluation & Demo purposes)
     */
    public function switchRole(string $role)
    {
        $user = match($role) {
            'super_admin' => User::where('role', 'super_admin')->first(),
            'branch_manager' => User::where('role', 'branch_manager')->first(),
            'maker_officer' => User::where('role', 'maker_officer')->first(),
            'teller' => User::where('role', 'teller')->first(),
            'compliance_officer' => User::where('role', 'compliance_officer')->first(),
            'customer_bilal' => User::where('email', 'bilal.farooq@example.com')->first(),
            'customer_tariq' => User::where('email', 'tariq.mehmood@example.com')->first(),
            'customer_pep' => User::where('email', 'malik.aslam@example.com')->first(),
            default => User::where('role', 'super_admin')->first(),
        };

        if ($user) {
            Auth::login($user);
            return $user->isCustomer() 
                ? redirect()->route('portal.dashboard')->with('success', "Switched context to Customer: {$user->name}")
                : redirect()->route('cbs.dashboard')->with('success', "Switched context to {$user->name} ({$user->role})");
        }

        return back()->with('error', 'User for target role not found.');
    }
}
