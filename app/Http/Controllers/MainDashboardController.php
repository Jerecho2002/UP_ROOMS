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
        $apiUrl = env('SYSTEM_A_API_URL') . '/inventoryitems';
        $token = env('SYSTEM_A_API_TOKEN');

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
        ])->get($apiUrl);

        $inventoryitems = $response->successful()
            ? $response->json()
            : [];
        
        $search = $request->input('search');

        return inertia('MainDashboard', [
            'inventoryitems' => $inventoryitems,
            'rooms' => $service->getMainDashboard($search),
        ]);
    }

}
