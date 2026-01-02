<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BuildingController extends Controller
{
    public function index()
    {
        return Inertia::render('BuildingDashboard');
    }

    public function getAll(Request $request)
    {
        $search = $request->query('search');
        $query = Building::with(['college:id,college_name,college_code'])
            ->orderBy('building_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('building_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('college', function ($q) use ($search) {
                      $q->where('college_name', 'like', "%{$search}%");
                  });
            });
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'total_floors' => 'nullable|integer|min:1',
            'total_rooms' => 'nullable|integer|min:0',
            'has_elevator' => 'boolean',
            'has_parking' => 'boolean',
            'restroom_count' => 'nullable|integer|min:0',
            'ramp_count' => 'nullable|integer|min:0',
            'college_id' => 'nullable|exists:colleges,id',
        ]);

        $building = Building::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Building created successfully',
            'data' => $building->load('college')
        ], 201);
    }

    public function update(Request $request, Building $building)
    {
        $validated = $request->validate([
            'building_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'total_floors' => 'nullable|integer|min:1',
            'total_rooms' => 'nullable|integer|min:0',
            'has_elevator' => 'boolean',
            'has_parking' => 'boolean',
            'restroom_count' => 'nullable|integer|min:0',
            'ramp_count' => 'nullable|integer|min:0',
            'college_id' => 'nullable|exists:colleges,id',
        ]);

        $building->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Building updated successfully',
            'data' => $building->load('college')
        ]);
    }

    public function destroy(Building $building)
    {
        $building->delete();

        return response()->json([
            'success' => true,
            'message' => 'Building deleted successfully'
        ]);
    }
}
