<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Room;
use App\Models\UserAccount;
use App\Models\Term;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index()
    {
        return Inertia::render('Schedule');
    }

    public function getAll(Request $request)
    {
        $search = $request->query('search');
        $query = Schedule::with([
                'room:id,room_name,room_code',
                'faculty:id,first_name,last_name',
                'requester:id,first_name,last_name',
                'term:id,term_name'
            ])
            ->orderBy('date', 'desc')
            ->orderBy('start_time');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('event_title', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%")
                  ->orWhere('faculty_name', 'like', "%{$search}%")
                  ->orWhere('cfic_id', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('room', function ($q) use ($search) {
                      $q->where('room_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('faculty', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'event_title' => 'required|string|max:255',
            'event_type' => 'required|in:class,meeting,event,other',
            'course_code' => 'nullable|string|max:50',
            'course_name' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:50',
            'faculty_name' => 'nullable|string|max:255',
            'faculty_id' => 'nullable|exists:user_accounts,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'day_of_week' => 'required|string|max:20',
            'number_of_participants' => 'nullable|integer|min:1',
            'requester_id' => 'nullable|exists:user_accounts,id',
            'requester_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'agenda' => 'nullable|string',
            'organizer' => 'nullable|string|max:255',
            'equipment_needed' => 'nullable|array',
            'additional_requirements' => 'nullable|array',
            'status' => 'required|in:approved,pending,cancelled,completed',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|array',
            'term_id' => 'nullable|exists:terms,id',
            'cfic_id' => 'nullable|string|max:100',
        ]);

        // Calculate day of week from date
        if (empty($validated['day_of_week']) && isset($validated['date'])) {
            $validated['day_of_week'] = Carbon::parse($validated['date'])->format('l');
        }

        $schedule = Schedule::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Schedule created successfully',
            'data' => $schedule->load(['room', 'faculty', 'requester', 'term'])
        ], 201);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'event_title' => 'required|string|max:255',
            'event_type' => 'required|in:class,meeting,event,other',
            'course_code' => 'nullable|string|max:50',
            'course_name' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:50',
            'faculty_name' => 'nullable|string|max:255',
            'faculty_id' => 'nullable|exists:user_accounts,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'day_of_week' => 'required|string|max:20',
            'number_of_participants' => 'nullable|integer|min:1',
            'requester_id' => 'nullable|exists:user_accounts,id',
            'requester_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'agenda' => 'nullable|string',
            'organizer' => 'nullable|string|max:255',
            'equipment_needed' => 'nullable|array',
            'additional_requirements' => 'nullable|array',
            'status' => 'required|in:approved,pending,cancelled,completed',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|array',
            'term_id' => 'nullable|exists:terms,id',
            'cfic_id' => 'nullable|string|max:100',
        ]);

        $schedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully',
            'data' => $schedule->load(['room', 'faculty', 'requester', 'term'])
        ]);
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }
}
