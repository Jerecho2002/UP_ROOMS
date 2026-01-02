<?php

namespace App\Services;

use App\Models\Department;
use App\Models\UserAccount;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
    public function getDepartmentStats($departmentId)
    {
        $department = Department::findOrFail($departmentId);

        return [
            'department_info' => [
                'name' => $department->department_name,
                'code' => $department->department_code,
                'college' => $department->college->college_name ?? 'N/A',
                'head' => $department->head ? $department->head->first_name . ' ' . $department->head->last_name : 'N/A'
            ],
            'user_stats' => [
                'total_faculty' => UserAccount::where('department_id', $departmentId)
                    ->where('user_type', 'faculty')
                    ->count(),
                'total_students' => UserAccount::where('department_id', $departmentId)
                    ->where('user_type', 'student')
                    ->count(),
                'total_staff' => UserAccount::where('department_id', $departmentId)
                    ->where('user_type', 'staff')
                    ->count(),
            ],
            'resource_stats' => [
                'total_rooms' => DB::table('rooms')
                    ->where('department_id', $departmentId)
                    ->count(),
                'available_rooms' => DB::table('rooms')
                    ->where('department_id', $departmentId)
                    ->where('status', 'available')
                    ->count(),
                'occupied_rooms' => DB::table('rooms')
                    ->where('department_id', $departmentId)
                    ->where('status', 'occupied')
                    ->count(),
            ],
            'equipment_stats' => [
                'total_equipment' => DB::table('equipment')
                    ->where('department_id', $departmentId)
                    ->count(),
                'available_equipment' => DB::table('equipment')
                    ->where('department_id', $departmentId)
                    ->where('status', 'available')
                    ->count(),
                'in_use_equipment' => DB::table('equipment')
                    ->where('department_id', $departmentId)
                    ->where('status', 'in_use')
                    ->count(),
            ],
            'schedule_stats' => [
                'total_schedules' => DB::table('schedules')
                    ->join('rooms', 'schedules.room_id', '=', 'rooms.id')
                    ->where('rooms.department_id', $departmentId)
                    ->count(),
                'approved_schedules' => DB::table('schedules')
                    ->join('rooms', 'schedules.room_id', '=', 'rooms.id')
                    ->where('rooms.department_id', $departmentId)
                    ->where('schedules.status', 'approved')
                    ->count(),
                'pending_schedules' => DB::table('schedules')
                    ->join('rooms', 'schedules.room_id', '=', 'rooms.id')
                    ->where('rooms.department_id', $departmentId)
                    ->where('schedules.status', 'pending')
                    ->count(),
            ]
        ];
    }

    public function getDepartmentsByCollege($collegeId)
    {
        return Department::where('college_id', $collegeId)
            ->with('college', 'head')
            ->orderBy('department_name')
            ->get()
            ->map(function ($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->department_name,
                    'code' => $dept->department_code,
                    'college' => $dept->college->college_name,
                    'head' => $dept->head ? $dept->head->full_name : 'Vacant',
                    'total_users' => UserAccount::where('department_id', $dept->id)->count(),
                    'total_rooms' => DB::table('rooms')->where('department_id', $dept->id)->count()
                ];
            });
    }

    public function validateDepartmentData(array $data)
    {
        $errors = [];

        // Check if college exists
        if (isset($data['college_id']) && !DB::table('colleges')->where('id', $data['college_id'])->exists()) {
            $errors['college_id'] = 'Selected college does not exist.';
        }

        // Check if department head exists
        if (isset($data['department_head_id']) && !DB::table('user_accounts')->where('id', $data['department_head_id'])->exists()) {
            $errors['department_head_id'] = 'Selected department head does not exist.';
        }

        // Check department code uniqueness
        if (isset($data['department_code'])) {
            $existing = Department::where('department_code', $data['department_code']);
            if (isset($data['id'])) {
                $existing->where('id', '!=', $data['id']);
            }
            if ($existing->exists()) {
                $errors['department_code'] = 'Department code already exists.';
            }
        }

        return $errors;
    }
}
