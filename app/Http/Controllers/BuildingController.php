<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index()
    {
        $buildings = Building::all();

        // Must match Vue file name exactly
        return Inertia::render('Building_Dashboard', [
            'buildings' => $buildings
        ]);
    }
}
