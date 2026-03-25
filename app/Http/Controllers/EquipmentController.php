<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Models\Equipment;
use App\Services\EquipmentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EquipmentController extends Controller
{
    public function __construct(
        protected EquipmentService $equipmentService,
    ) {}
    public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->input('search');
        $equipment = $this->equipmentService->getEquipment($perPage, $search);

        return Inertia::render('Equipment', [
            'equipment' => $equipment,
            'filters'   => $request->only('search'),
        ]);
    }

    public function store(StoreEquipmentRequest $request)
    {
        Equipment::create($request->validated());

        return redirect()->back()->with('success', 'Equipment created successfully.');
    }

    public function update(UpdateEquipmentRequest $request, Equipment $equipment)
    {
        $equipment->update($request->validated());

        return redirect()->back()->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        return redirect()
            ->back()
            ->with('success', 'Equipment deleted successfully.');
    }
}
