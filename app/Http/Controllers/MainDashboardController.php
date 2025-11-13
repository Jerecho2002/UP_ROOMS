<?php

namespace App\Http\Controllers;

use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\MainDashboardService;

class MainDashboardController
{
    public function index(Request $request, MainDashboardService $service)
    {
        $response = Http::get('http://127.0.0.1:8000/api/inventoryitems');
        $inventoryitems = $response->json();
        
        $search = $request->input('search');

        return inertia('MainDashboard', [
            'inventoryitems' => $inventoryitems,
            'rooms' => $service->getMainDashboard($search),
        ]);
    }

}
