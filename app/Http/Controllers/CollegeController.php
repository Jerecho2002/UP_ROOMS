<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\UserAccount;
use App\Models\Building;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollegeController extends Controller
{
    public function index(Request $request)
    {
        $colleges = College::with('dean')
            ->orderBy('college_name')
            ->paginate(20);

        $deans = UserAccount::where('user_type', 'dean')
            ->orWhere('roles', 'like', '%dean%')
            ->get();

        return response()->json([
            'colleges' => $colleges,
            'deans' => $deans,
            'stats' => [
                'total' => College::count(),
                'with_dean' => College::whereNotNull('dean_id')->count(),
                'avg_buildings' => round(College::withCount('buildings')->get()->avg('buildings_count'), 2),
                'avg_departments' => round(College::withCount('departments')->get()->avg('departments_count'), 2),
            ]
        ]);
    }

    public function getAll(Request $request)
    {
        $query = College::with('dean');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('college_name', 'like', "%{$search}%")
                  ->orWhere('college_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('has_dean')) {
            if (filter_var($request->has_dean, FILTER_VALIDATE_BOOLEAN)) {
                $query->whereNotNull('dean_id');
            } else {
                $query->whereNull('dean_id');
            }
        }

        $sortField = $request->get('sort_field', 'college_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    public function getStats(College $college)
    {
        $buildingsCount = $college->buildings()->count();
        $departmentsCount = $college->departments()->count();
        $roomsCount = $college->rooms()->count();
        $equipmentCount = $college->equipment()->count();
        $usersCount = $college->userAccounts()->count();

        return response()->json([
            'college' => $college->load('dean'),
            'stats' => [
                'buildings' => $buildingsCount,
                'departments' => $departmentsCount,
                'rooms' => $roomsCount,
                'equipment' => $equipmentCount,
                'users' => $usersCount,
                'available_rooms' => $college->rooms()->where('status', 'available')->count(),
                'available_equipment' => $college->equipment()->where('status', 'available')->count(),
                'building_types' => DB::table('buildings')
                    ->where('college_id', $college->id)
                    ->select(
                        DB::raw("SUM(CASE WHEN has_elevator = 1 THEN 1 ELSE 0 END) as with_elevator"),
                        DB::raw("SUM(CASE WHEN has_parking = 1 THEN 1 ELSE 0 END) as with_parking")
                    )
                    ->first(),
            ],
            'departments' => $college->departments()->with('head')->limit(5)->get(),
            'buildings' => $college->buildings()->limit(5)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'college_name' => 'required|string|max:255|unique:colleges,college_name',
            'college_code' => 'required|string|max:50|unique:colleges,college_code',
            'description' => 'nullable|string',
            'dean_id' => 'nullable|exists:user_accounts,id',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        $college = College::create($validated);

        return response()->json([
            'message' => 'College created successfully',
            'college' => $college->load('dean'),
        ], 201);
    }

    public function update(Request $request, College $college)
    {
        $validated = $request->validate([
            'college_name' => 'sometimes|required|string|max:255|unique:colleges,college_name,' . $college->id,
            'college_code' => 'sometimes|required|string|max:50|unique:colleges,college_code,' . $college->id,
            'description' => 'nullable|string',
            'dean_id' => 'nullable|exists:user_accounts,id',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        $college->update($validated);

        return response()->json([
            'message' => 'College updated successfully',
            'college' => $college->load('dean'),
        ]);
    }

    public function destroy(College $college)
    {
        // Check if college has dependencies
        if ($college->buildings()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete college with existing buildings'
            ], 422);
        }

        if ($college->departments()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete college with existing departments'
            ], 422);
        }

        if ($college->userAccounts()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete college with existing users'
            ], 422);
        }

        $college->delete();

        return response()->json([
            'message' => 'College deleted successfully'
        ]);
    }
}
