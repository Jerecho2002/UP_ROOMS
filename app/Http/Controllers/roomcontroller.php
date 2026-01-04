<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use Inertia\Inertia;
use App\Models\College;
use App\Models\Building;
use App\Models\RoomType;
use App\Models\Schedule;
use App\Models\Department;
use App\Models\UserAccount;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with(['building', 'college', 'department', 'roomType', 'assignedUser'])
            ->orderBy('room_name')
            ->paginate(20);

        $buildings = Building::all();
        $colleges = College::all();
        $departments = Department::all();
        $roomTypes = RoomType::all();
        $users = UserAccount::whereIn('user_type', ['faculty', 'staff'])->get();

        return Inertia::render('room', [
            'rooms' => $rooms,
            'buildings' => $buildings,
            'colleges' => $colleges,
            'departments' => $departments,
            'room_types' => $roomTypes,
            'users' => $users,
            'stats' => [
                'total' => Room::count(),
                'available' => Room::where('status', 'available')->count(),
                'occupied' => Room::where('status', 'occupied')->count(),
                'maintenance' => Room::where('status', 'maintenance')->count(),
                'avg_capacity' => round(Room::avg('capacity'), 2),
            ]
        ]);
    }

    public function getAll(Request $request)
    {
        $query = Room::with(['building', 'college', 'department', 'roomType', 'assignedUser']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('room_name', 'like', "%{$search}%")
                  ->orWhere('room_code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->has('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->has('college_id')) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('min_capacity')) {
            $query->where('capacity', '>=', $request->min_capacity);
        }

        $sortField = $request->get('sort_field', 'room_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    public function show($id)
    {
        $room = Room::with(['building', 'college', 'department', 'roomType', 'assignedUser', 'equipment', 'schedules' => function($query) {
            $query->where('date', '>=', today())
                  ->orderBy('date')
                  ->orderBy('start_time')
                  ->limit(10);
        }])->findOrFail($id);

        return response()->json($room);
    }

    public function getAvailability($id, Request $request)
    {
        $room = Room::findOrFail($id);

        $date = $request->get('date', today()->format('Y-m-d'));

        $schedules = Schedule::where('room_id', $id)
            ->where('date', $date)
            ->where('status', 'approved')
            ->orderBy('start_time')
            ->get();

        // Generate time slots (assuming 8 AM to 8 PM)
        $timeSlots = [];
        $startTime = Carbon::createFromTime(8, 0, 0);
        $endTime = Carbon::createFromTime(20, 0, 0);

        $current = $startTime->copy();

        while ($current < $endTime) {
            $slotEnd = $current->copy()->addHour();

            $isBooked = $schedules->contains(function($schedule) use ($current, $slotEnd) {
                $scheduleStart = Carbon::parse($schedule->start_time);
                $scheduleEnd = Carbon::parse($schedule->end_time);

                return ($current >= $scheduleStart && $current < $scheduleEnd) ||
                       ($slotEnd > $scheduleStart && $slotEnd <= $scheduleEnd) ||
                       ($current <= $scheduleStart && $slotEnd >= $scheduleEnd);
            });

            $timeSlots[] = [
                'start' => $current->format('H:i'),
                'end' => $slotEnd->format('H:i'),
                'available' => !$isBooked,
            ];

            $current->addHour();
        }

        return response()->json([
            'room' => $room,
            'date' => $date,
            'time_slots' => $timeSlots,
            'bookings' => $schedules,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'room_code' => 'required|string|max:50|unique:rooms,room_code',
            'building_id' => 'nullable|exists:buildings,id',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'assigned_user_id' => 'nullable|exists:user_accounts,id',
            'floor_number' => 'nullable|integer',
            'location' => 'nullable|string|max:500',
            'capacity' => 'nullable|integer|min:1',
            'area_sqm' => 'nullable|numeric|min:0',
            'facilities' => 'nullable|array',
            'status' => 'required|in:available,occupied,maintenance,closed',
            'notes' => 'nullable|string',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'message' => 'Room created successfully',
            'room' => $room->load(['building', 'college', 'department', 'roomType', 'assignedUser']),
        ], 201);
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_name' => 'sometimes|required|string|max:255',
            'room_code' => 'sometimes|required|string|max:50|unique:rooms,room_code,' . $room->id,
            'building_id' => 'nullable|exists:buildings,id',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'room_type_id' => 'nullable|exists:room_types,id',
            'assigned_user_id' => 'nullable|exists:user_accounts,id',
            'floor_number' => 'nullable|integer',
            'location' => 'nullable|string|max:500',
            'capacity' => 'nullable|integer|min:1',
            'area_sqm' => 'nullable|numeric|min:0',
            'facilities' => 'nullable|array',
            'status' => 'sometimes|required|in:available,occupied,maintenance,closed',
            'notes' => 'nullable|string',
        ]);

        $room->update($validated);

        return response()->json([
            'message' => 'Room updated successfully',
            'room' => $room->load(['building', 'college', 'department', 'roomType', 'assignedUser']),
        ]);
    }

    public function destroy(Room $room)
    {
        // Check if room has dependencies
        if ($room->equipment()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete room with existing equipment'
            ], 422);
        }

        if ($room->schedules()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete room with existing schedules'
            ], 422);
        }

        $room->delete();

        return response()->json([
            'message' => 'Room deleted successfully'
        ]);
    }
}
