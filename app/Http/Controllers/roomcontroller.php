<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Building;
use App\Models\College;
use App\Models\Department;
use App\Models\RoomType;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoomController extends Controller
{
    public function index()
    {
        return Inertia::render('Room');
    }

    public function getAll(Request $request)
    {
        $search = $request->query('search');
        $query = Room::with([
                'building:id,building_name',
                'college:id,college_name',
                'department:id,department_name',
                'roomType:id,room_type_name',
                'assignedUser:id,first_name,last_name'
            ])
            ->orderBy('room_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('room_name', 'like', "%{$search}%")
                  ->orWhere('room_code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('building', function ($q) use ($search) {
                      $q->where('building_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('college', function ($q) use ($search) {
                      $q->where('college_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function ($q) use ($search) {
                      $q->where('department_name', 'like', "%{$search}%");
                  });
            });
        }

        return response()->json($query->paginate(10));
    }

    public function show($id)
    {
        $room = Room::with([
            'building',
            'college',
            'department',
            'roomType',
            'assignedUser',
            'equipment',
            'schedules'
        ])->findOrFail($id);

        return response()->json($room);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_name' => 'required|string|max:100',
            'room_code' => 'required|string|max:50|unique:rooms',
            'building_id' => 'nullable|exists:buildings,id',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'assigned_user_id' => 'nullable|exists:user_accounts,id',
            'floor_number' => 'nullable|integer',
            'location' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'area_sqm' => 'nullable|numeric|min:0',
            'facilities' => 'nullable|array',
            'status' => 'required|in:available,occupied,maintenance,closed',
            'notes' => 'nullable|string',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room created successfully',
            'data' => $room->load(['building', 'college', 'department', 'roomType', 'assignedUser'])
        ], 201);
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_name' => 'required|string|max:100',
            'room_code' => 'required|string|max:50|unique:rooms,room_code,' . $room->id,
            'building_id' => 'nullable|exists:buildings,id',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'assigned_user_id' => 'nullable|exists:user_accounts,id',
            'floor_number' => 'nullable|integer',
            'location' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1',
            'area_sqm' => 'nullable|numeric|min:0',
            'facilities' => 'nullable|array',
            'status' => 'required|in:available,occupied,maintenance,closed',
            'notes' => 'nullable|string',
        ]);

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Room updated successfully',
            'data' => $room->load(['building', 'college', 'department', 'roomType', 'assignedUser'])
        ]);
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully'
        ]);
    }
}
