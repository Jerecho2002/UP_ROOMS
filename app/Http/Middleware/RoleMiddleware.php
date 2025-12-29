<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admins and SYSADMINS have access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (!$user->hasAnyRole($roles)) {
            // Redirect to appropriate dashboard based on role
            return match($user->role) {
                'Staff' => redirect('/schedule'),
                'Faculty' => redirect('/room'),
                'DPTAPR', 'AO', 'ADPD', 'OCS' => redirect('/MainDashboard'),
                default => redirect('/schedule'),
            };
        }

        return $next($request);
    }
}
