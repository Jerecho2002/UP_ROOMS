<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Services\BuildingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class BuildingController extends Controller
{
    public function __construct(
        protected BuildingService $buildingService,
    ){}
     public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->input('search');
        $builds = $this->buildingService->getBuildings($perPage, $search);

        return Inertia::render('BuildingDashboard', [
            'builds' => $builds,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            $building = Building::create($validator->validated());

            return response()->json([
                'success' => true,
                'data' => $building->load('college'),
                'message' => 'Building created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create building: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Building $building)
    {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            $building->update($validator->validated());

            return response()->json([
                'success' => true,
                'data' => $building->load('college'),
                'message' => 'Building updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update building: ' . $e->getMessage()
            ], 500);
        }
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
