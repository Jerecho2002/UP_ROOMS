<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Term;
use Inertia\Inertia;
use App\Models\Schedule;
use App\Models\UserAccount;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = Schedule::with(['room', 'faculty', 'requester', 'term'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        $rooms = Room::where('status', 'available')->get();
        $faculty = UserAccount::where('user_type', 'faculty')->get();
        $requesters = UserAccount::whereIn('user_type', ['faculty', 'staff'])->get();
        $terms = Term::where('status', 'active')->get();

        return Inertia::render('schedule',[
            'schedules' => $schedules,
            'rooms' => $rooms,
            'faculty' => $faculty,
            'requesters' => $requesters,
            'terms' => $terms,
            // 'stats' => $this->getScheduleStats(),
        ]);
    }

    public function getAll(Request $request)
    {
        $query = Schedule::with(['room', 'faculty', 'requester', 'term']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event_title', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%")
                  ->orWhere('faculty_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->has('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->has('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        $sortField = $request->get('sort_field', 'date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    public function getStats()
    {
        $today = today()->format('Y-m-d');

        $total = Schedule::count();
        $approved = Schedule::where('status', 'approved')->count();
        $pending = Schedule::where('status', 'pending')->count();
        $cancelled = Schedule::where('status', 'cancelled')->count();

        $todayCount = Schedule::where('date', $today)->count();
        $upcomingCount = Schedule::where('date', '>', $today)->where('status', 'approved')->count();

        $byEventType = Schedule::select('event_type', \DB::raw('COUNT(*) as count'))
            ->groupBy('event_type')
            ->get()
            ->pluck('count', 'event_type')
            ->toArray();

        $recentApprovals = Schedule::where('status', 'approved')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get(['id', 'event_title', 'room_id', 'date', 'updated_at']);

        return [
            'total' => $total,
            'approved' => $approved,
            'pending' => $pending,
            'cancelled' => $cancelled,
            'today' => $todayCount,
            'upcoming' => $upcomingCount,
            'by_event_type' => $byEventType,
            'recent_approvals' => $recentApprovals,
        ];
    }

    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'schedule_id' => 'nullable|exists:schedules,id', // For updating existing schedule
        ]);

        $room = Room::findOrFail($validated['room_id']);

        $conflictingSchedules = Schedule::where('room_id', $validated['room_id'])
            ->where('date', $validated['date'])
            ->where('status', 'approved')
            ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
                });
            });

        if ($request->has('schedule_id')) {
            $conflictingSchedules->where('id', '!=', $validated['schedule_id']);
        }

        $conflicts = $conflictingSchedules->get();

        $isAvailable = $conflicts->isEmpty();

        return response()->json([
            'available' => $isAvailable,
            'room' => $room,
            'requested_slot' => [
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'duration' => Carbon::parse($validated['end_time'])->diffInMinutes(Carbon::parse($validated['start_time'])) . ' minutes',
            ],
            'conflicts' => $conflicts,
            'message' => $isAvailable ? 'Room is available' : 'Room is not available at the requested time',
        ]);
    }

    public function approve($id)
    {
        $schedule = Schedule::findOrFail($id);

        // Check for conflicts before approving
        $conflicts = Schedule::where('room_id', $schedule->room_id)
            ->where('date', $schedule->date)
            ->where('status', 'approved')
            ->where('id', '!=', $schedule->id)
            ->where(function($query) use ($schedule) {
                $query->where(function($q) use ($schedule) {
                    $q->where('start_time', '<', $schedule->end_time)
                      ->where('end_time', '>', $schedule->start_time);
                });
            })
            ->exists();

        if ($conflicts) {
            return response()->json([
                'message' => 'Cannot approve schedule due to time conflicts'
            ], 422);
        }

        $schedule->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Schedule approved successfully',
            'schedule' => $schedule->load(['room', 'faculty', 'requester']),
        ]);
    }

    public function getRoomSchedules($roomId, Request $request)
    {
        $room = Room::findOrFail($roomId);

        $query = Schedule::where('room_id', $roomId)
            ->with(['faculty', 'requester'])
            ->orderBy('date')
            ->orderBy('start_time');

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('date', [$request->date_from, $request->date_to]);
        }

        $schedules = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'room' => $room,
            'schedules' => $schedules,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'event_title' => 'required|string|max:255',
            'event_type' => 'required|in:class,meeting,exam,event,other',
            'course_code' => 'nullable|string|max:50',
            'course_name' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:50',
            'faculty_name' => 'nullable|string|max:255',
            'faculty_id' => 'nullable|exists:user_accounts,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'day_of_week' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'number_of_participants' => 'nullable|integer|min:1',
            'requester_id' => 'required|exists:user_accounts,id',
            'requester_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'agenda' => 'nullable|string',
            'organizer' => 'nullable|string|max:255',
            'equipment_needed' => 'nullable|array',
            'additional_requirements' => 'nullable|array',
            'status' => 'required|in:pending,approved,cancelled',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|array',
            'term_id' => 'nullable|exists:terms,id',
            'cfic_id' => 'nullable|string|max:50',
        ]);

        // Auto-generate day_of_week from date if not provided
        if (empty($validated['day_of_week']) && !empty($validated['date'])) {
            $date = Carbon::parse($validated['date']);
            $validated['day_of_week'] = strtolower($date->englishDayOfWeek);
        }

        // Check for conflicts if status is approved
        if ($validated['status'] === 'approved') {
            $conflicts = Schedule::where('room_id', $validated['room_id'])
                ->where('date', $validated['date'])
                ->where('status', 'approved')
                ->where(function($query) use ($validated) {
                    $query->where(function($q) use ($validated) {
                        $q->where('start_time', '<', $validated['end_time'])
                          ->where('end_time', '>', $validated['start_time']);
                    });
                })
                ->exists();

            if ($conflicts) {
                return response()->json([
                    'message' => 'Cannot create schedule due to time conflicts'
                ], 422);
            }
        }

        $schedule = Schedule::create($validated);

        return response()->json([
            'message' => 'Schedule created successfully',
            'schedule' => $schedule->load(['room', 'faculty', 'requester', 'term']),
        ], 201);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'room_id' => 'sometimes|required|exists:rooms,id',
            'event_title' => 'sometimes|required|string|max:255',
            'event_type' => 'sometimes|required|in:class,meeting,exam,event,other',
            'course_code' => 'nullable|string|max:50',
            'course_name' => 'nullable|string|max:255',
            'section' => 'nullable|string|max:50',
            'faculty_name' => 'nullable|string|max:255',
            'faculty_id' => 'nullable|exists:user_accounts,id',
            'date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
            'day_of_week' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'number_of_participants' => 'nullable|integer|min:1',
            'requester_id' => 'sometimes|required|exists:user_accounts,id',
            'requester_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'agenda' => 'nullable|string',
            'organizer' => 'nullable|string|max:255',
            'equipment_needed' => 'nullable|array',
            'additional_requirements' => 'nullable|array',
            'status' => 'sometimes|required|in:pending,approved,cancelled',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|array',
            'term_id' => 'nullable|exists:terms,id',
            'cfic_id' => 'nullable|string|max:50',
        ]);

        // Check for conflicts if status is being changed to approved
        if (isset($validated['status']) && $validated['status'] === 'approved') {
            $conflicts = Schedule::where('room_id', $validated['room_id'] ?? $schedule->room_id)
                ->where('date', $validated['date'] ?? $schedule->date)
                ->where('status', 'approved')
                ->where('id', '!=', $schedule->id)
                ->where(function($query) use ($validated, $schedule) {
                    $query->where(function($q) use ($validated, $schedule) {
                        $q->where('start_time', '<', $validated['end_time'] ?? $schedule->end_time)
                          ->where('end_time', '>', $validated['start_time'] ?? $schedule->start_time);
                    });
                })
                ->exists();

            if ($conflicts) {
                return response()->json([
                    'message' => 'Cannot approve schedule due to time conflicts'
                ], 422);
            }
        }

        $schedule->update($validated);

        return response()->json([
            'message' => 'Schedule updated successfully',
            'schedule' => $schedule->load(['room', 'faculty', 'requester', 'term']),
        ]);
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return response()->json([
            'message' => 'Schedule deleted successfully'
        ]);
    }
}
