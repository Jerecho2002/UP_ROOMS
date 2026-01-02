<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Room;
use App\Models\Building;
use App\Models\College;
use App\Models\Department;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Equipment');
    }

    public function getAll(Request $request)
    {
        $search = $request->query('search');
        $query = Equipment::with([
                'room:id,room_name,room_code',
                'building:id,building_name',
                'college:id,college_name',
                'department:id,department_name',
                'assignedUser:id,first_name,last_name'
            ])
            ->orderBy('equipment_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('equipment_name', 'like', "%{$search}%")
                  ->orWhere('inventory_id', 'like', "%{$search}%")
                  ->orWhere('property_id', 'like', "%{$search}%")
                  ->orWhere('cfic_id', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('room', function ($q) use ($search) {
                      $q->where('room_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('building', function ($q) use ($search) {
                      $q->where('building_name', 'like', "%{$search}%");
                  });
            });
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:100',
            'inventory_id' => 'required|string|max:50|unique:equipment',
            'property_id' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'room_id' => 'nullable|exists:rooms,id',
            'building_id' => 'nullable|exists:buildings,id',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'cfic_id' => 'nullable|string|max:100',
            'status' => 'required|in:available,in_use,maintenance,damaged,retired',
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
            'success' => true,
            'message' => 'Equipment created successfully',
            'data' => $equipment->load(['room', 'building', 'college', 'department', 'assignedUser'])
        ], 201);
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'equipment_name' => 'required|string|max:100',
            'inventory_id' => 'required|string|max:50|unique:equipment,inventory_id,' . $equipment->id,
            'property_id' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'room_id' => 'nullable|exists:rooms,id',
            'building_id' => 'nullable|exists:buildings,id',
            'college_id' => 'nullable|exists:colleges,id',
            'department_id' => 'nullable|exists:departments,id',
            'cfic_id' => 'nullable|string|max:100',
            'status' => 'required|in:available,in_use,maintenance,damaged,retired',
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
            'success' => true,
            'message' => 'Equipment updated successfully',
            'data' => $equipment->load(['room', 'building', 'college', 'department', 'assignedUser'])
        ]);
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Equipment deleted successfully'
        ]);
    }
}
