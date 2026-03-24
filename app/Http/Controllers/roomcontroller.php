<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Building;
use App\Models\College;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\UserAccount;
use App\Services\RoomService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RoomController extends Controller
{
    public function __construct(
        protected RoomService $roomService,
    ) {}

    public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->input('search');

        $rooms = $this->roomService->getRooms($perPage, $search);
        $buildings = Building::all();
        $colleges = College::all();
        $roomTypes = RoomType::all();
        $equipment = Equipment::all();
        $departments = Department::all();
        $users = UserAccount::all();

        return Inertia::render('Rooms', [
            'rooms' => $rooms,
            'buildings' => $buildings,
            'colleges' => $colleges,
            'roomTypes' => $roomTypes,
            'equipment' => $equipment,
            'departments' => $departments,
            'users' => $users,
        ]);
    }

    public function store(StoreRoomRequest $request, RoomService $service)
    {
        $service->create($request->validated());

        return redirect()->back()->with('success', 'Room created successfully.');
    }


    public function update(UpdateRoomRequest $request, Room $room, RoomService $service)
    {
        $service->update($room, $request->validated());

        return redirect()->back()->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        try {
            $room->delete();

            return redirect()
                ->back()
                ->with('success', 'Room deleted successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete room. It may be in use.');
        }
    }
}
