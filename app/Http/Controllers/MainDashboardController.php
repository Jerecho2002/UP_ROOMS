<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use App\Services\MainDashboardService;
use Illuminate\Http\Request;

class MainDashboardController
{
    public function index(Request $request, MainDashboardService $service)
    {
        $search = $request->input('search');

        return inertia('MainDashboard', [
            'rooms' => $service->getMainDashboard($search),
        ]);
    }

}
