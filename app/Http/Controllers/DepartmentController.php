<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\College;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Department');
    }

    public function getAll(Request $request)
    {
        try {
            $search = $request->query('search', '');
            $perPage = $request->query('per_page', 10);

            // First, check if colleges table exists to avoid circular dependency
            if (!\Schema::hasTable('colleges')) {
                // Return empty if tables don't exist yet
                return response()->json([
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => $perPage
                ]);
            }

            $query = Department::query();

            // Check if user_accounts table exists before trying to join
            if (\Schema::hasTable('user_accounts')) {
                $query->with([
                    'college:id,college_name,college_code',
                    'head:id,first_name,last_name,username,email'
                ]);
            } else {
                $query->with('college:id,college_name,college_code');
            }

            $query->orderBy('department_name', 'asc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('department_name', 'like', "%{$search}%")
                      ->orWhere('department_code', 'like', "%{$search}%")
                      ->orWhere('office_location', 'like', "%{$search}%")
                      ->orWhere('contact_email', 'like', "%{$search}%")
                      ->orWhere('contact_phone', 'like', "%{$search}%")
                      ->orWhereHas('college', function ($q) use ($search) {
                          $q->where('college_name', 'like', "%{$search}%")
                            ->orWhere('college_code', 'like', "%{$search}%");
                      });

                    // Only add head search if table exists
                    if (\Schema::hasTable('user_accounts')) {
                        $q->orWhereHas('head', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('username', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                        });
                    }
                });
            }

            $departments = $query->paginate($perPage);

            // Transform data for frontend
            $transformedData = $departments->through(function ($department) {
                $data = [
                    'id' => $department->id,
                    'department_name' => $department->department_name,
                    'department_code' => $department->department_code,
                    'description' => $department->description,
                    'office_location' => $department->office_location,
                    'contact_email' => $department->contact_email,
                    'contact_phone' => $department->contact_phone,
                    'created_at' => $department->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $department->updated_at->format('Y-m-d H:i:s'),
                ];

                // Add college data if exists
                if ($department->college) {
                    $data['college'] = [
                        'id' => $department->college->id,
                        'college_name' => $department->college->college_name,
                        'college_code' => $department->college->college_code,
                    ];
                } else {
                    $data['college'] = null;
                }

                // Add head data if exists and table available
                if ($department->head && \Schema::hasTable('user_accounts')) {
                    $data['head'] = [
                        'id' => $department->head->id,
                        'name' => $department->head->first_name . ' ' . $department->head->last_name,
                        'username' => $department->head->username,
                        'email' => $department->head->email,
                    ];
                } else {
                    $data['head'] = null;
                }

                return $data;
            });

            return response()->json([
                'data' => $transformedData,
                'total' => $departments->total(),
                'current_page' => $departments->currentPage(),
                'last_page' => $departments->lastPage(),
                'per_page' => $departments->perPage()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching departments: ' . $e->getMessage());
            return response()->json([
                'data' => [],
                'total' => 0,
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 10,
                'error' => 'Unable to fetch departments. Please check database tables.'
            ], 500);
        }
    }

    public function getStats($departmentId = null)
    {
        try {
            // Check if tables exist
            if (!\Schema::hasTable('departments') || !\Schema::hasTable('user_accounts') || !\Schema::hasTable('rooms')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database tables not ready'
                ], 500);
            }

            $stats = [
                'total_faculty' => 0,
                'total_students' => 0,
                'total_rooms' => 0,
                'total_equipment' => 0,
                'active_schedules' => 0
            ];

            if ($departmentId) {
                $department = Department::find($departmentId);
                if (!$department) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Department not found'
                    ], 404);
                }

                // Get faculty count (users with faculty type)
                $stats['total_faculty'] = UserAccount::where('department_id', $departmentId)
                    ->where('user_type', 'faculty')
                    ->count();

                // Get student count
                $stats['total_students'] = UserAccount::where('department_id', $departmentId)
                    ->where('user_type', 'student')
                    ->count();

                // Get room count
                $stats['total_rooms'] = DB::table('rooms')
                    ->where('department_id', $departmentId)
                    ->count();

                // Get equipment count
                $stats['total_equipment'] = DB::table('equipment')
                    ->where('department_id', $departmentId)
                    ->count();

                // Get active schedules count
                $stats['active_schedules'] = DB::table('schedules')
                    ->join('rooms', 'schedules.room_id', '=', 'rooms.id')
                    ->where('rooms.department_id', $departmentId)
                    ->where('schedules.status', 'approved')
                    ->whereDate('schedules.date', '>=', now())
                    ->count();

                // Add department info
                $stats['department_info'] = [
                    'name' => $department->department_name,
                    'code' => $department->department_code,
                    'college' => $department->college ? $department->college->college_name : 'N/A',
                    'head' => $department->head ? $department->head->first_name . ' ' . $department->head->last_name : 'N/A'
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting department stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'department_name' => 'required|string|max:255',
                'department_code' => 'nullable|string|max:50|unique:departments,department_code',
                'college_id' => 'required|exists:colleges,id',
                'department_head_id' => 'nullable|exists:user_accounts,id',
                'description' => 'nullable|string',
                'office_location' => 'nullable|string|max:255',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create department
            $department = Department::create($validator->validated());

            // Load relationships
            $department->load([
                'college:id,college_name,college_code',
                'head:id,first_name,last_name,username,email'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department created successfully',
                'data' => $this->transformDepartment($department)
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error creating department: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Find department
            $department = Department::findOrFail($id);

            // Validate request
            $validator = Validator::make($request->all(), [
                'department_name' => 'required|string|max:255',
                'department_code' => 'nullable|string|max:50|unique:departments,department_code,' . $department->id,
                'college_id' => 'required|exists:colleges,id',
                'department_head_id' => 'nullable|exists:user_accounts,id',
                'description' => 'nullable|string',
                'office_location' => 'nullable|string|max:255',
                'contact_email' => 'nullable|email|max:255',
                'contact_phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update department
            $department->update($validator->validated());

            // Refresh relationships
            $department->refresh();
            $department->load([
                'college:id,college_name,college_code',
                'head:id,first_name,last_name,username,email'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Department updated successfully',
                'data' => $this->transformDepartment($department)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error updating department: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $department = Department::findOrFail($id);

            // Check if department has dependent records
            $userCount = UserAccount::where('department_id', $id)->count();
            $roomCount = DB::table('rooms')->where('department_id', $id)->count();
            $equipmentCount = DB::table('equipment')->where('department_id', $id)->count();

            if ($userCount > 0 || $roomCount > 0 || $equipmentCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete department. It has ' .
                               ($userCount ? "$userCount users, " : "") .
                               ($roomCount ? "$roomCount rooms, " : "") .
                               ($equipmentCount ? "$equipmentCount equipment" : "") .
                               ' associated with it.',
                    'details' => [
                        'users_count' => $userCount,
                        'rooms_count' => $roomCount,
                        'equipment_count' => $equipmentCount
                    ]
                ], 422);
            }

            $departmentName = $department->department_name;
            $department->delete();

            return response()->json([
                'success' => true,
                'message' => "Department '{$departmentName}' deleted successfully"
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error deleting department: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete department',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available colleges for dropdown
     */
    public function getColleges()
    {
        try {
            if (!\Schema::hasTable('colleges')) {
                return response()->json(['data' => []]);
            }

            $colleges = College::select('id', 'college_name', 'college_code')
                ->orderBy('college_name')
                ->get();

            return response()->json(['data' => $colleges]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * Get available department heads for dropdown
     */
    public function getDepartmentHeads()
    {
        try {
            if (!\Schema::hasTable('user_accounts')) {
                return response()->json(['data' => []]);
            }

            $heads = UserAccount::where('user_type', 'faculty')
                ->orWhere('user_type', 'admin')
                ->select('id', 'first_name', 'last_name', 'username', 'email')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name . ' (' . $user->username . ')',
                        'username' => $user->username,
                        'email' => $user->email
                    ];
                });

            return response()->json(['data' => $heads]);
        } catch (\Exception $e) {
            return response()->json(['data' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * Helper method to transform department data
     */
    private function transformDepartment($department)
    {
        return [
            'id' => $department->id,
            'department_name' => $department->department_name,
            'department_code' => $department->department_code,
            'description' => $department->description,
            'office_location' => $department->office_location,
            'contact_email' => $department->contact_email,
            'contact_phone' => $department->contact_phone,
            'college' => $department->college ? [
                'id' => $department->college->id,
                'name' => $department->college->college_name,
                'code' => $department->college->college_code
            ] : null,
            'head' => $department->head ? [
                'id' => $department->head->id,
                'name' => $department->head->first_name . ' ' . $department->head->last_name,
                'username' => $department->head->username,
                'email' => $department->head->email
            ] : null,
            'created_at' => $department->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $department->updated_at->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Quick stats for dashboard
     */
    public function quickStats()
    {
        try {
            if (!\Schema::hasTable('departments')) {
                return response()->json([
                    'total_departments' => 0,
                    'departments_with_head' => 0,
                    'departments_without_head' => 0
                ]);
            }

            $totalDepartments = Department::count();
            $departmentsWithHead = Department::whereNotNull('department_head_id')->count();

            return response()->json([
                'total_departments' => $totalDepartments,
                'departments_with_head' => $departmentsWithHead,
                'departments_without_head' => $totalDepartments - $departmentsWithHead
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting department quick stats: ' . $e->getMessage());
            return response()->json([
                'total_departments' => 0,
                'departments_with_head' => 0,
                'departments_without_head' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
