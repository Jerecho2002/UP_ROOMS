<?php

namespace App\Http\Controllers\API;

use App\Models\Room;
use App\Models\College;
use Illuminate\Http\Request;

class RoomController
{
    public function index()
    {
        $rooms = Room::all();

        $colleges = College::select('id', 'college_name')->get();

        $rooms = $rooms->map(function ($room) use ($colleges) {
            $college = $colleges->firstWhere('id', $room->college_id);
            $room->college_name = $college->college_name ?? 'Unknown';
            return $room;
        });
        
        return response()->json([
            'rooms' => $rooms,
        ]);
    }
}
