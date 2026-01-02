<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuildingController extends Controller
{
    public function index(Request $request)
    {
        $buildings = Building::with('college')
            ->orderBy('building_name')
            ->paginate(20);

        $colleges = College::all();

        return response()->json([
            'buildings' => $buildings,
            'colleges' => $colleges,
            'stats' => [
                'total' => Building::count(),
                'with_elevator' => Building::where('has_elevator', true)->count(),
                'with_parking' => Building::where('has_parking', true)->count(),
                'avg_rooms' => round(Building::avg('total_rooms'), 2),
            ]
        ]);
    }

    public function getAll(Request $request)
    {
        $query = Building::with('college');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('building_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('college_id')) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->has('has_elevator')) {
            $query->where('has_elevator', filter_var($request->has_elevator, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('has_parking')) {
            $query->where('has_parking', filter_var($request->has_parking, FILTER_VALIDATE_BOOLEAN));
        }

        $sortField = $request->get('sort_field', 'building_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    public function getStats(Building $building)
    {
        $rooms = $building->rooms()->count();
        $equipment = $building->equipment()->count();
        $availableRooms = $building->rooms()->where('status', 'available')->count();

        return response()->json([
            'building' => $building->load('college'),
            'stats' => [
                'total_rooms' => $rooms,
                'available_rooms' => $availableRooms,
                'total_equipment' => $equipment,
                'occupancy_rate' => $rooms > 0 ? round(($availableRooms / $rooms) * 100, 2) : 0,
                'room_types' => DB::table('rooms')
                    ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                    ->where('rooms.building_id', $building->id)
                    ->select('room_types.room_type_name', DB::raw('COUNT(*) as count'))
                    ->groupBy('room_types.room_type_name')
                    ->get(),
            ],
            'recent_activities' => $this->getBuildingActivities($building),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string',
            'total_floors' => 'required|integer|min:1',
            'total_rooms' => 'required|integer|min:0',
            'has_elevator' => 'boolean',
            'has_parking' => 'boolean',
            'restroom_count' => 'integer|min:0',
            'ramp_count' => 'integer|min:0',
            'college_id' => 'required|exists:colleges,id',
        ]);

        $building = Building::create($validated);

        return response()->json([
            'message' => 'Building created successfully',
            'building' => $building->load('college'),
        ], 201);
    }

    public function update(Request $request, Building $building)
    {
        $validated = $request->validate([
            'building_name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:500',
            'description' => 'nullable|string',
            'total_floors' => 'sometimes|required|integer|min:1',
            'total_rooms' => 'sometimes|required|integer|min:0',
            'has_elevator' => 'boolean',
            'has_parking' => 'boolean',
            'restroom_count' => 'integer|min:0',
            'ramp_count' => 'integer|min:0',
            'college_id' => 'sometimes|required|exists:colleges,id',
        ]);

        $building->update($validated);

        return response()->json([
            'message' => 'Building updated successfully',
            'building' => $building->load('college'),
        ]);
    }

    public function destroy(Building $building)
    {
        // Check if building has rooms or equipment
        if ($building->rooms()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete building with existing rooms'
            ], 422);
        }

        if ($building->equipment()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete building with existing equipment'
            ], 422);
        }

        $building->delete();

        return response()->json([
            'message' => 'Building deleted successfully'
        ]);
    }

    private function getBuildingActivities(Building $building)
    {
        $activities = [];

        // Recent room changes in this building
        $recentRooms = $building->rooms()
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentRooms as $room) {
            $activities[] = [
                'type' => 'room',
                'action' => 'updated',
                'description' => "Room {$room->room_name} was updated",
                'time' => $room->updated_at->diffForHumans(),
            ];
        }

        // Recent equipment changes
        $recentEquipment = $building->equipment()
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentEquipment as $equipment) {
            $activities[] = [
                'type' => 'equipment',
                'action' => 'updated',
                'description' => "Equipment {$equipment->equipment_name} was updated",
                'time' => $equipment->updated_at->diffForHumans(),
            ];
        }

        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }
}
