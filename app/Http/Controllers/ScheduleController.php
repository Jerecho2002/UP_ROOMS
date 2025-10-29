<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule; // Assuming you have a Schedule model
use Inertia\Inertia;

class ScheduleController extends Controller
{
    /**
     * Store a newly created schedule appointment resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'appointmentDay' => 'required|date_format:Y-m-d',
            'time' => 'nullable|string|max:100', // Time range string
            'list' => 'nullable|string|max:100',
            // 'room_id' => 'nullable|exists:rooms,id', // Add this if linking to a room
        ]);

        // 2. Database Creation
        Schedule::create([
            'title' => $validated['title'],
            'category' => $validated['list'], // Mapping 'list' from Vue to 'category' in model
            'appointment_day' => $validated['appointmentDay'],
            'time_slot' => $validated['time'], // Mapping 'time' from Vue to 'time_slot'
        ]);

        // 3. Redirection (Inertia refresh)
        return redirect()->route('schedule')->with('success', 'Appointment scheduled successfully.');
    }
}