<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Schedule;
use App\Models\Equipment;
use App\Models\UserAccount;
use App\Models\Building;
use App\Models\College;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function generateRoomUtilizationReport($startDate, $endDate)
    {
        $schedules = Schedule::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->select('room_id', DB::raw('count(*) as total_schedules'), DB::raw('sum(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours'))
            ->groupBy('room_id')
            ->with('room:id,room_name,room_code,capacity')
            ->get();

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'total_rooms' => Room::count(),
            'rooms_with_schedules' => $schedules->count(),
            'room_utilization' => $schedules->map(function ($item) use ($startDate, $endDate) {
                $totalDays = Carbon::parse($startDate)->diffInDays($endDate) + 1;
                $availableHours = $totalDays * 9; // Assuming 9 working hours per day

                return [
                    'room_id' => $item->room_id,
                    'room_name' => $item->room->room_name,
                    'room_code' => $item->room->room_code,
                    'total_schedules' => $item->total_schedules,
                    'total_hours' => $item->total_hours ?? 0,
                    'utilization_percentage' => $availableHours > 0 ? min(100, round(($item->total_hours / $availableHours) * 100, 2)) : 0,
                    'avg_daily_hours' => $totalDays > 0 ? round($item->total_hours / $totalDays, 2) : 0,
                ];
            }),
            'summary' => [
                'total_schedules' => $schedules->sum('total_schedules'),
                'total_hours' => $schedules->sum('total_hours'),
                'avg_utilization' => $schedules->avg('utilization_percentage'),
            ]
        ];
    }

    public function generateEquipmentStatusReport()
    {
        $equipment = Equipment::select('status', DB::raw('count(*) as count'), DB::raw('sum(quantity) as total_quantity'), DB::raw('sum(purchase_price) as total_value'))
            ->groupBy('status')
            ->get();

        $equipmentByCollege = Equipment::whereNotNull('college_id')
            ->select('college_id', DB::raw('count(*) as count'), DB::raw('sum(purchase_price) as total_value'))
            ->groupBy('college_id')
            ->with('college:id,college_name')
            ->get()
            ->map(function ($item) {
                return [
                    'college_name' => $item->college->college_name ?? 'Unknown',
                    'count' => $item->count,
                    'total_value' => $item->total_value ?? 0,
                ];
            });

        return [
            'status_summary' => $equipment->map(function ($item) {
                return [
                    'status' => $item->status,
                    'count' => $item->count,
                    'quantity' => $item->total_quantity ?? 0,
                    'value' => $item->total_value ?? 0,
                ];
            }),
            'college_distribution' => $equipmentByCollege,
            'total_summary' => [
                'total_items' => Equipment::count(),
                'total_quantity' => Equipment::sum('quantity'),
                'total_value' => Equipment::sum('purchase_price'),
                'avg_value_per_item' => Equipment::avg('purchase_price'),
            ]
        ];
    }

    public function generateUserActivityReport($startDate, $endDate)
    {
        $users = UserAccount::whereBetween('created_at', [$startDate, $endDate])
            ->orWhereBetween('last_login_at', [$startDate, $endDate])
            ->with(['college:id,college_name', 'department:id,department_name'])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'username' => $user->username,
                    'user_type' => $user->user_type,
                    'college' => $user->college ? $user->college->college_name : null,
                    'department' => $user->department ? $user->department->department_name : null,
                    'account_status' => $user->account_status,
                    'created_at' => $user->created_at,
                    'last_login_at' => $user->last_login_at,
                    'days_since_last_login' => $user->last_login_at ? Carbon::parse($user->last_login_at)->diffInDays() : null,
                ];
            });

        $activityByDay = UserAccount::whereNotNull('last_login_at')
            ->whereBetween('last_login_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(last_login_at) as date'), DB::raw('count(*) as login_count'))
            ->groupBy(DB::raw('DATE(last_login_at)'))
            ->orderBy('date')
            ->get();

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'user_summary' => [
                'total_users' => UserAccount::count(),
                'active_users' => UserAccount::where('account_status', 'active')->count(),
                'new_users' => UserAccount::whereBetween('created_at', [$startDate, $endDate])->count(),
                'users_with_login' => UserAccount::whereNotNull('last_login_at')
                    ->whereBetween('last_login_at', [$startDate, $endDate])
                    ->count(),
                'inactive_users' => UserAccount::where('account_status', 'inactive')->count(),
            ],
            'users' => $users,
            'daily_activity' => $activityByDay,
        ];
    }

    public function generateScheduleReport($startDate, $endDate)
    {
        $schedules = Schedule::whereBetween('date', [$startDate, $endDate])
            ->with(['room:id,room_name', 'faculty:id,first_name,last_name'])
            ->get();

        $byEventType = $schedules->groupBy('event_type')->map->count();
        $byStatus = $schedules->groupBy('status')->map->count();
        $byRoom = $schedules->groupBy('room.room_name')->map->count()->sortDesc()->take(10);
        $byFaculty = $schedules->whereNotNull('faculty')
            ->groupBy(function ($schedule) {
                return $schedule->faculty ? $schedule->faculty->first_name . ' ' . $schedule->faculty->last_name : 'Unknown';
            })
            ->map->count()
            ->sortDesc()
            ->take(10);

        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate,
            ],
            'total_schedules' => $schedules->count(),
            'by_event_type' => $byEventType,
            'by_status' => $byStatus,
            'top_rooms' => $byRoom,
            'top_faculty' => $byFaculty,
            'daily_count' => $schedules->groupBy('date')->map->count()->sortKeys(),
            'hourly_distribution' => $this->getHourlyDistribution($schedules),
        ];
    }

    private function getHourlyDistribution($schedules)
    {
        $hourly = array_fill(0, 24, 0);

        foreach ($schedules as $schedule) {
            $startHour = (int) date('H', strtotime($schedule->start_time));
            $endHour = (int) date('H', strtotime($schedule->end_time));

            for ($hour = $startHour; $hour < $endHour; $hour++) {
                if ($hour >= 0 && $hour < 24) {
                    $hourly[$hour]++;
                }
            }
        }

        return $hourly;
    }

    public function generateBuildingReport()
    {
        $buildings = Building::withCount(['rooms', 'equipment'])
            ->with(['college:id,college_name'])
            ->get()
            ->map(function ($building) {
                $roomStats = DB::table('rooms')
                    ->where('building_id', $building->id)
                    ->select(
                        DB::raw('count(*) as total'),
                        DB::raw('sum(case when status = "available" then 1 else 0 end) as available'),
                        DB::raw('sum(case when status = "occupied" then 1 else 0 end) as occupied'),
                        DB::raw('sum(case when status = "maintenance" then 1 else 0 end) as maintenance'),
                        DB::raw('sum(capacity) as total_capacity')
                    )
                    ->first();

                return [
                    'id' => $building->id,
                    'name' => $building->building_name,
                    'college' => $building->college ? $building->college->college_name : null,
                    'address' => $building->address,
                    'total_rooms' => $building->rooms_count,
                    'total_equipment' => $building->equipment_count,
                    'room_stats' => [
                        'available' => $roomStats->available ?? 0,
                        'occupied' => $roomStats->occupied ?? 0,
                        'maintenance' => $roomStats->maintenance ?? 0,
                        'total_capacity' => $roomStats->total_capacity ?? 0,
                    ],
                    'occupancy_rate' => $building->rooms_count > 0 ?
                        round(($roomStats->occupied ?? 0) / $building->rooms_count * 100, 2) : 0,
                ];
            });

        return [
            'total_buildings' => $buildings->count(),
            'total_rooms' => $buildings->sum('total_rooms'),
            'total_equipment' => $buildings->sum('total_equipment'),
            'avg_occupancy_rate' => $buildings->avg('occupancy_rate'),
            'buildings' => $buildings,
        ];
    }
}
