<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MainDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('MainDashboard', [
            'user' => $user,
            'stats' => [
                'total_users' => \App\Models\User::count(),
                'total_buildings' => 5, // Replace with actual model
                'total_colleges' => 10, // Replace with actual model
                'total_rooms' => 50, // Replace with actual model
            ]
        ]);
    }
}
