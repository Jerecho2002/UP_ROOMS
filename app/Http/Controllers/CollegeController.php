<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class CollegeController extends Controller
{
    /**
     * Display college dashboard
     */
    public function index()
    {
        return Inertia::render('CollegeDashboard', [
            'initialColleges' => College::with(['dean:id,first_name,last_name'])
                ->orderBy('college_name')
                ->get()
                ->map(function ($college) {
                    return [
                        'id' => $college->id,
                        'college' => $college->college_name,
                        'department' => $college->departments()->first()?->department_name ?? 'Not specified',
                        'building' => $college->buildings()->first()?->building_name ?? 'Not specified',
                        'description' => $college->description,
                        'college_code' => $college->college_code,
                        'contact_email' => $college->contact_email,
                        'contact_phone' => $college->contact_phone,
                        'dean_name' => $college->dean ? "{$college->dean->first_name} {$college->dean->last_name}" : 'Not assigned',
                    ];
                })
        ]);
    }

    /**
     * Get all colleges
     */
    public function getAll(Request $request)
    {
        try {
            $query = College::with(['dean:id,first_name,last_name'])
                ->orderBy('college_name', 'asc');

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('college_name', 'like', "%{$search}%")
                      ->orWhere('college_code', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('dean', function ($q2) use ($search) {
                          $q2->where('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            }

            $colleges = $query->get()->map(function ($college) {
                return [
                    'id' => $college->id,
                    'college' => $college->college_name,
                    'department' => $college->departments()->first()?->department_name ?? 'Not specified',
                    'building' => $college->buildings()->first()?->building_name ?? 'Not specified',
                    'description' => $college->description,
                    'college_code' => $college->college_code,
                    'contact_email' => $college->contact_email,
                    'contact_phone' => $college->contact_phone,
                    'dean_name' => $college->dean ? "{$college->dean->first_name} {$college->dean->last_name}" : 'Not assigned',
                ];
            });

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
     * Store a new college
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'college' => 'required|string|max:150|unique:colleges,college_name',
            'college_code' => 'nullable|string|max:50|unique:colleges,college_code',
            'department' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'description' => 'nullable|string',
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
            $college = College::create([
                'college_name' => $request->college,
                'college_code' => $request->college_code,
                'description' => $request->description,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
            ]);

            // If building is provided, create it
            if ($request->building) {
                $college->buildings()->create([
                    'building_name' => $request->building,
                    'address' => 'Not specified',
                ]);
            }

            // If department is provided, create it
            if ($request->department) {
                $college->departments()->create([
                    'department_name' => $request->department,
                    'department_code' => strtoupper(substr($request->department, 0, 3)),
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $college->id,
                    'college' => $college->college_name,
                    'department' => $request->department ?? 'Not specified',
                    'building' => $request->building ?? 'Not specified',
                    'description' => $college->description,
                    'college_code' => $college->college_code,
                    'contact_email' => $college->contact_email,
                    'contact_phone' => $college->contact_phone,
                    'dean_name' => 'Not assigned',
                ],
                'message' => 'College created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create college: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a college
     */
    public function update(Request $request, College $college)
    {
        $validator = Validator::make($request->all(), [
            'college' => 'required|string|max:150|unique:colleges,college_name,' . $college->id,
            'college_code' => 'nullable|string|max:50|unique:colleges,college_code,' . $college->id,
            'department' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'description' => 'nullable|string',
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
            $college->update([
                'college_name' => $request->college,
                'college_code' => $request->college_code,
                'description' => $request->description,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
            ]);

            // Update building if exists, otherwise create
            $building = $college->buildings()->first();
            if ($request->building) {
                if ($building) {
                    $building->update(['building_name' => $request->building]);
                } else {
                    $college->buildings()->create([
                        'building_name' => $request->building,
                        'address' => 'Not specified',
                    ]);
                }
            }

            // Update department if exists, otherwise create
            $department = $college->departments()->first();
            if ($request->department) {
                if ($department) {
                    $department->update(['department_name' => $request->department]);
                } else {
                    $college->departments()->create([
                        'department_name' => $request->department,
                        'department_code' => strtoupper(substr($request->department, 0, 3)),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $college->id,
                    'college' => $college->college_name,
                    'department' => $request->department ?? ($department ? $department->department_name : 'Not specified'),
                    'building' => $request->building ?? ($building ? $building->building_name : 'Not specified'),
                    'description' => $college->description,
                    'college_code' => $college->college_code,
                    'contact_email' => $college->contact_email,
                    'contact_phone' => $college->contact_phone,
                    'dean_name' => $college->dean ? "{$college->dean->first_name} {$college->dean->last_name}" : 'Not assigned',
                ],
                'message' => 'College updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update college: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a college
     */
    public function destroy(College $college)
    {
        try {
            // Check if college has related records
            if ($college->departments()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete college that has departments. Please delete departments first.'
                ], 422);
            }

            if ($college->buildings()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete college that has buildings. Please delete buildings first.'
                ], 422);
            }

            $college->delete();

            return response()->json([
                'success' => true,
                'message' => 'College deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete college: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get college statistics
     */
    public function getStats(College $college)
    {
        try {
            $stats = [
                'total_departments' => $college->departments()->count(),
                'total_buildings' => $college->buildings()->count(),
                'total_rooms' => $college->rooms()->count(),
                'available_rooms' => $college->rooms()->where('status', 'available')->count(),
                'total_equipment' => $college->equipment()->count(),
                'available_equipment' => $college->equipment()->where('status', 'available')->count(),
                'total_users' => $college->userAccounts()->count(),
                'active_users' => $college->userAccounts()->where('account_status', 'active')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'College stats fetched successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch college stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all deans for dropdown
     */
    public function getDeans()
    {
        try {
            $deans = UserAccount::where('user_type', 'faculty')
                ->orWhere('user_type', 'admin')
                ->select('id', 'first_name', 'last_name', 'email')
                ->orderBy('first_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $deans,
                'message' => 'Deans fetched successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch deans: ' . $e->getMessage()
            ], 500);
        }
    }
}
