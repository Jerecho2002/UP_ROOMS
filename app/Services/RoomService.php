<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Schedule;
use App\Models\Equipment;
use Illuminate\Support\Facades\DB;

class RoomService
{
    public function getAllRooms($search = null, $filters = [])
    {
        $query = Room::with([
                'building:id,building_name',
                'college:id,college_name',
                'department:id,department_name',
                'roomType:id,room_type_name',
                'assignedUser:id,first_name,last_name'
            ])
            ->withCount(['equipment', 'schedules'])
            ->orderBy('room_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('room_name', 'like', "%{$search}%")
                  ->orWhere('room_code', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('building', function ($q) use ($search) {
                      $q->where('building_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('college', function ($q) use ($search) {
                      $q->where('college_name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['building_id'])) {
            $query->where('building_id', $filters['building_id']);
        }

        if (!empty($filters['college_id'])) {
            $query->where('college_id', $filters['college_id']);
        }

        if (!empty($filters['room_type_id'])) {
            $query->where('room_type_id', $filters['room_type_id']);
        }

        return $query->paginate(10);
    }

    public function getRoomDetails($roomId)
    {
        $room = Room::with([
            'building',
            'college',
            'department',
            'roomType',
            'assignedUser',
            'equipment',
            'schedules' => function($q) {
                $q->where('date', '>=', now()->format('Y-m-d'))
                  ->orderBy('date')
                  ->orderBy('start_time');
            }
        ])->findOrFail($roomId);

        return $room;
    }

    public function getRoomAvailability($roomId, $date = null)
    {
        $date = $date ?: now()->format('Y-m-d');

        $schedules = Schedule::where('room_id', $roomId)
            ->where('date', $date)
            ->where('status', 'approved')
            ->orderBy('start_time')
            ->get();

        return [
            'date' => $date,
            'schedules' => $schedules,
            'available_slots' => $this->calculateAvailableSlots($schedules),
        ];
    }

    private function calculateAvailableSlots($schedules)
    {
        // Default working hours: 8 AM to 5 PM
        $startHour = 8;
        $endHour = 17;
        $slots = [];

        // Convert schedules to time slots
        $bookedSlots = [];
        foreach ($schedules as $schedule) {
            $bookedSlots[] = [
                'start' => strtotime($schedule->start_time),
                'end' => strtotime($schedule->end_time),
            ];
        }

        // Generate available slots
        $current = strtotime($startHour . ':00');
        $end = strtotime($endHour . ':00');

        while ($current < $end) {
            $slotEnd = $current + 3600; // 1 hour slots
            $isAvailable = true;

            foreach ($bookedSlots as $booked) {
                if (($current >= $booked['start'] && $current < $booked['end']) ||
                    ($slotEnd > $booked['start'] && $slotEnd <= $booked['end']) ||
                    ($current <= $booked['start'] && $slotEnd >= $booked['end'])) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $slots[] = [
                    'start' => date('H:i', $current),
                    'end' => date('H:i', $slotEnd),
                ];
            }

            $current = $slotEnd;
        }

        return $slots;
    }

    public function createRoom($data)
    {
        return Room::create($data);
    }

    public function updateRoom($roomId, $data)
    {
        $room = Room::findOrFail($roomId);
        $room->update($data);
        return $room;
    }

    public function deleteRoom($roomId)
    {
        $room = Room::findOrFail($roomId);
        $room->delete();
        return true;
    }
}
