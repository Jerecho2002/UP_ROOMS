<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Models\College;
use App\Services\BuildingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class BuildingController extends Controller
{
    public function __construct(
        protected BuildingService $buildingService,
    ) {}
    public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->input('search');
        $buildings = $this->buildingService->getBuildings($perPage, $search);
        $colleges = College::all();

        return Inertia::render('BuildingDashboard', [
            'buildings' => $buildings,
            'colleges' => $colleges,
        ]);
    }

    public function store(StoreBuildingRequest $request)
    {
        Building::create($request->validated());

        return redirect()->back()->with('success', 'Building created successfully.');
    }

    public function update(UpdateBuildingRequest $request, Building $building)
    {
        $building->update($request->validated());

        return redirect()->back()->with('success', 'Building updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Building $building)
    {
        try {
            // Check if building has rooms or equipment
            if ($building->rooms()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete building that has rooms. Please delete rooms first.'
                ], 422);
            }

            if ($building->equipment()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete building that has equipment. Please transfer equipment first.'
                ], 422);
            }

            $building->delete();

            return response()->json([
                'success' => true,
                'message' => 'Building deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete building: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get building statistics
     */
    public function getStats(Building $building)
    {
        try {
            $stats = [
                'total_rooms' => $building->rooms()->count(),
                'available_rooms' => $building->rooms()->where('status', 'available')->count(),
                'occupied_rooms' => $building->rooms()->where('status', 'occupied')->count(),
                'total_equipment' => $building->equipment()->count(),
                'available_equipment' => $building->equipment()->where('status', 'available')->count(),
                'floors' => $building->total_floors,
                'total_room_capacity' => $building->rooms()->sum('capacity'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Building stats fetched successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch building stats: ' . $e->getMessage()
            ], 500);
        }
    }
}
