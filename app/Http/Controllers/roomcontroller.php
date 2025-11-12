<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room; // Assuming you have a Room model
use Inertia\Inertia;

class RoomController
{
    /**
     * Store a newly created room resource in storage.
     * Corresponds to the 'save' event from AddRoomModal.vue
     */
    public function store(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'room' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'building' => 'nullable|string|max:255',
            'college' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'roomType' => 'nullable|string|max:255',
        ]);

        // 2. Database Creation
        Room::create([
            'name' => $validated['room'],
            'capacity' => $validated['capacity'],
            'building_name' => $validated['building'],
            'college_name' => $validated['college'],
            'location_details' => $validated['location'],
            'room_type' => $validated['roomType'],
        ]);

        // 3. Redirection (Inertia refresh)
        return back()->with('success', 'Room added successfully.');
    }
}