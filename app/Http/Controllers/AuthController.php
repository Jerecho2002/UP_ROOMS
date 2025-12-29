<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return Inertia::render('Login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check credentials
        $user = \App\Models\User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'username' => 'Invalid credentials.',
            ]);
        }

        // Login user
        Auth::login($user);

        $request->session()->regenerate();

        // Redirect based on role
        return match($user->role) {
            'Admin', 'SYSADMIN' => redirect()->intended('/MainDashboard'),
            'Staff' => redirect()->intended('/schedule'),
            'Faculty' => redirect()->intended('/room'),
            'DPTAPR', 'AO', 'ADPD', 'OCS' => redirect()->intended('/MainDashboard'),
            default => redirect()->intended('/schedule'),
        };
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
