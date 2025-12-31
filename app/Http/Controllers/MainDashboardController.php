<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MainDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get user from session
        $user = $request->session()->get('user');

        if (!$user) {
            return redirect('/login');
        }

        return Inertia::render('MainDashboard', [
            'user' => $user
        ]);
    }
}
