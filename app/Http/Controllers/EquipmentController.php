<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Room;
use App\Models\Building;
use App\Models\College;
use App\Models\Department;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $equipment = Equipment::with(['room', 'building', 'college', 'department', 'assignedUser'])
            ->orderBy('equipment_name')
            ->paginate(20);

        $rooms = Room::all();
        $buildings = Building::all();
        $colleges = College::all();
        $departments = Department::all();
        $users = UserAccount::whereIn('user_type', ['faculty', 'staff'])->get();

        return response()->json([
            'equipment' => $equipment,
            'rooms' => $rooms,
            'buildings' => $buildings,
            'colleges' => $colleges,
            'departments' => $departments,
            'users' => $users,
            'stats' => $this->getEquipmentStats(),
        ]);
    }

    public function getAll(Request $request)
    {
        $query = Equipment::with(['room', 'building', 'college', 'department', 'assignedUser']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('equipment_name', 'like', "%{$search}%")
                  ->orWhere('inventory_id', 'like', "%{$search}%")
                  ->orWhere('property_id', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->has('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->has('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->has('college_id')) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('cfic_id')) {
            $query->where('cfic_id', $request->cfic_id);
        }

        $sortField = $request->get('sort_field', 'equipment_name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortField, $sortOrder);

        return response()->json($query->paginate($request->get('per_page', 20)));
    }

    public function getStats()
    {
        $total = Equipment::count();
        $available = Equipment::where('status', 'available')->count();
        $inUse = Equipment::where('status', 'in_use')->count();
        $maintenance = Equipment::where('status', 'maintenance')->count();
        $retired = Equipment::where('status', 'retired')->count();

        $totalValue = Equipment::sum('purchase_price');
        $avgValue = $total > 0 ? $totalValue / $total : 0;

        $recentAdditions = Equipment::orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'equipment_name', 'created_at']);

        $statusDistribution = Equipment::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return [
            'total' => $total,
            'available' => $available,
            'in_use' => $inUse,
            'maintenance' => $maintenance,
            'retired' => $retired,
            'total_value' => $totalValue,
            'average_value' => round($avgValue, 2),
            'status_distribution' => $statusDistribution,
            'recent_additions' => $recentAdditions,
        ];
    }

    public function transfer($id, Request $request)
    {
        $equipment = Equipment::findOrFail($id);

        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'building_id' => 'nullable|exists:buildings,id',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_user_id' => 'nullable|exists:user_accounts,id',
            'transfer_notes' => 'nullable|string',
        ]);

        // Log the transfer
        $oldLocation = $equipment->location;

        $equipment->update($validated);

        $newLocation = $equipment->location;

        return response()->json([
            'message' => 'Equipment transferred successfully',
            'equipment' => $equipment->load(['room', 'building', 'college', 'department', 'assignedUser']),
            'transfer_info' => [
                'from' => $oldLocation,
                'to' => $newLocation,
                'notes' => $request->get('transfer_notes'),
                'transferred_at' => now(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:255',
            'inventory_id' => 'required|string|max:100|unique:equipment,inventory_id',
            'property_id' => 'nullable|string|max:100|unique:equipment,property_id',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'room_id' => 'nullable|exists:rooms,id',
            'building_id' => 'nullable|exists:buildings,id',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'cfic_id' => 'nullable|string|max:50',
            'status' => 'required|in:available,in_use,maintenance,retired',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'assigned_user_id' => 'nullable|exists:user_accounts,id',
            'specifications' => 'nullable|array',
        ]);

        $equipment = Equipment::create($validated);

        return response()->json([
            'message' => 'Equipment created successfully',
            'equipment' => $equipment->load(['room', 'building', 'college', 'department', 'assignedUser']),
        ], 201);
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'equipment_name' => 'sometimes|required|string|max:255',
            'inventory_id' => 'sometimes|required|string|max:100|unique:equipment,inventory_id,' . $equipment->id,
            'property_id' => 'nullable|string|max:100|unique:equipment,property_id,' . $equipment->id,
            'description' => 'nullable|string',
            'quantity' => 'sometimes|required|integer|min:1',
            'room_id' => 'nullable|exists:rooms,id',
            'building_id' => 'nullable|exists:buildings,id',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'cfic_id' => 'nullable|string|max:50',
            'status' => 'sometimes|required|in:available,in_use,maintenance,retired',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'assigned_user_id' => 'nullable|exists:user_accounts,id',
            'specifications' => 'nullable|array',
        ]);

        $equipment->update($validated);

        return response()->json([
            'message' => 'Equipment updated successfully',
            'equipment' => $equipment->load(['room', 'building', 'college', 'department', 'assignedUser']),
        ]);
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return response()->json([
            'message' => 'Equipment deleted successfully'
        ]);
    }
}
