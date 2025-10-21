<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * Display a listing of the buildings.
     * The BuildingsPage.vue will receive this data as props.
     */
    public function index()
    {
        // Mock data structure matching the expected format in BuildingsPage.vue
        $buildings = [
            [
                'id' => 1,
                'name' => 'Science Building',
                'address' => 'University Road, Cebu',
                'total_space' => '5000 sqm',
                'lift' => 'Yes',
                'parking' => true,
            ],
            [
                'id' => 2,
                'name' => 'Library Hall',
                'address' => 'Main Campus, Cebu City',
                'total_space' => '3000 sqm',
                'lift' => 'No',
                'parking' => false,
            ],
            [
                'id' => 3,
                'name' => 'Engineering Block',
                'address' => 'Tech Park, Cebu',
                'total_space' => '7000 sqm',
                'lift' => 'Yes',
                'parking' => true,
            ],
            [
                'id' => 4,
                'name' => 'Admin Office',
                'address' => 'Campus Center, Cebu',
                'total_space' => '2000 sqm',
                'lift' => 'No',
                'parking' => true,
            ],
            [
                'id' => 5,
                'name' => 'Innovation Hub',
                'address' => 'UP Cebu SRP',
                'total_space' => '4500 sqm',
                'lift' => 'Yes',
                'parking' => false,
            ],
        ];

        // The page rendered is 'BuildingsPage' (as inferred from your component's content)
        // This will automatically be resolved to resources/js/Pages/BuildingsPage.vue
        return Inertia::render('BuildingsPage', [
            'buildings' => $buildings
        ]);
    }
}
