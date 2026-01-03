<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\College;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    /**
     * Display the department page
     */
    public function index()
    {
        return Inertia::render('Department');
    }

    /**
     * Get all departments for the API
     */
    public function getAll(Request $request)
    {
        try {
            $query = Department::with([
                'college:id,college_name,college_code',
                'head:id,first_name,last_name,middle_name,email'
            ])->orderBy('department_name', 'asc');

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('department_name', 'like', "%{$search}%")
                      ->orWhere('department_code', 'like', "%{$search}%")
                      ->orWhere('office_location', 'like', "%{$search}%")
                      ->orWhereHas('college', function ($q2) use ($search) {
                          $q2->where('college_name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('head', function ($q2) use ($search) {
                          $q2->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by college
            if ($request->has('college_id') && $request->college_id) {
                $query->where('college_id', $request->college_id);
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $departments = $query->paginate($perPage);

            // Transform data for frontend
            $transformedDepartments = $departments->map(function ($department) {
                return [
                    'id' => $department->id,
                    'department_name' => $department->department_name,
                    'department_code' => $department->department_code,
                    'college' => $department->college ? $department->college->college_name : 'Not Assigned',
                    'college_id' => $department->college_id,
                    'dean' => $department->head ? $department->head->full_name : 'Not Assigned',
                    'department_head_id' => $department->department_head_id,
                    'description' => $department->description,
                    'office_location' => $department->office_location,
                    'contact_email' => $department->contact_email,
                    'contact_phone' => $department->contact_phone,
                    'created_at' => $department->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedDepartments,
                'meta' => [
                    'current_page' => $departments->currentPage(),
                    'per_page' => $departments->perPage(),
                    'total' => $departments->total(),
                    'last_page' => $departments->lastPage(),
                ],
                'message' => 'Departments fetched successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch departments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all colleges for dropdown
     */
    public function getColleges()
    {
        try {
            $colleges = College::select('id', 'college_name', 'college_code')
                ->orderBy('college_name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $colleges,
                'message' => 'Colleges fetched successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch colleges: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all potential department heads
     */
    public function getDepartmentHeads()
    {
        try {
            $heads = UserAccount::whereIn('user_type', ['faculty', 'admin', 'staff'])
                ->select('id', 'first_name', 'last_name', 'middle_name', 'email')
                ->orderBy('first_name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'full_name' => $user->full_name,
                        'email' => $user->email
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $heads,
                'message' => 'Department heads fetched successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch department heads: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new department
     */
    public function store(Request $request)
    {
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
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            $department = Department::create($validator->validated());

            // Load relationships for response
            $department->load(['college', 'head']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $department->id,
                    'department_name' => $department->department_name,
                    'department_code' => $department->department_code,
                    'college' => $department->college ? $department->college->college_name : 'Not Assigned',
                    'dean' => $department->head ? $department->head->full_name : 'Not Assigned',
                    'description' => $department->description,
                    'office_location' => $department->office_location,
                    'contact_email' => $department->contact_email,
                    'contact_phone' => $department->contact_phone,
                ],
                'message' => 'Department created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create department: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a department
     */
    public function update(Request $request, Department $department)
    {
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
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            $department->update($validator->validated());
            $department->load(['college', 'head']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $department->id,
                    'department_name' => $department->department_name,
                    'department_code' => $department->department_code,
                    'college' => $department->college ? $department->college->college_name : 'Not Assigned',
                    'dean' => $department->head ? $department->head->full_name : 'Not Assigned',
                    'description' => $department->description,
                    'office_location' => $department->office_location,
                    'contact_email' => $department->contact_email,
                    'contact_phone' => $department->contact_phone,
                ],
                'message' => 'Department updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update department: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a department
     */
    public function destroy(Department $department)
    {
        try {
            // Check if department has related records
            if ($department->rooms()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete department that has rooms. Please delete rooms first.'
                ], 422);
            }

            if ($department->equipment()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete department that has equipment. Please transfer equipment first.'
                ], 422);
            }

            if ($department->userAccounts()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete department that has users. Please reassign users first.'
                ], 422);
            }

            $departmentName = $department->department_name;
            $department->delete();

            return response()->json([
                'success' => true,
                'message' => "Department '{$departmentName}' deleted successfully",
                'deleted_name' => $departmentName
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete department: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get department statistics
     */
    public function getStats(Department $department)
    {
        try {
            $stats = [
                'total_rooms' => $department->rooms()->count(),
                'available_rooms' => $department->rooms()->where('status', 'available')->count(),
                'total_equipment' => $department->equipment()->count(),
                'available_equipment' => $department->equipment()->where('status', 'available')->count(),
                'total_users' => $department->userAccounts()->count(),
                'active_users' => $department->userAccounts()->where('account_status', 'active')->count(),
                'total_schedules' => $department->rooms()->withCount('schedules')->get()->sum('schedules_count'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Department stats fetched successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch department stats: ' . $e->getMessage()
            ], 500);
        }
    }
}
