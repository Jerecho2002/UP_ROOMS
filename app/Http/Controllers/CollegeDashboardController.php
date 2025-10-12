<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CollegeDashboardController extends Controller
{
    /**
     * Display the College Dashboard page using Inertia.
     */
    public function index()
    {
        // CRITICAL FIX: Ensure you use Inertia::render() instead of view()
        // The string 'college_dashboard' must match the filename in resources/js/Pages/
        return Inertia::render('college_dashboard', [
            // Example data passed to the Vue component (you can replace with real data)
            'stats' => [
                'classes' => 12,
                'students' => 540,
                'teachers' => 35,
                'messages' => 3,
            ]
        ]);
    }
}
