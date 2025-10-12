<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Building;
use App\Models\College;
use App\Models\RoomType;

class roomcontroller extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $rooms = Room::with(['building', 'college', 'roomType'])
            ->when($search, function ($query, $search) {
                $query->where('room_name', 'like', "%$search%");
            })
            ->get();

        return view('rooms.index', compact('rooms'));
    }

    public function show($id)
    {
        $room = Room::with(['building', 'college', 'roomType'])->findOrFail($id);
        return view('rooms.show', compact('room'));
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        $buildings = Building::all();
        $colleges = College::all();
        $roomTypes = RoomType::all();

        return view('rooms.edit', compact('room', 'buildings', 'colleges', 'roomTypes'));
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $room->update($request->all());

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }
}
