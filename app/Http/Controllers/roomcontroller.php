<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Building;
use App\Models\College;
use App\Models\Department;
use App\Models\UserAccount;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Services\RoomService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

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
        $departments = Department::all();
        $users = UserAccount::all();

        return Inertia::render('Rooms', [
            'rooms' => $rooms,
            'buildings' => $buildings,
            'colleges' => $colleges,
            'departments' => $departments,
            'roomTypes' => $roomTypes,
            'users' => $users,
        ]);
    }

    public function store(StoreRoomRequest $request)
    {
        try {
            Room::create($request->validated());

            return redirect()
                ->back()
                ->with('success', 'Room created successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to create room. Please try again.');
        }
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        try {
            $room->update($request->validated());

            return redirect()
                ->back()
                ->with('success', 'Room updated successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update room. Please try again.');
        }
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
