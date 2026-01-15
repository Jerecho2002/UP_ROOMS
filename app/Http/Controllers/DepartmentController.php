<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\Building;
use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Models\Department;
use App\Models\College;
use App\Services\DepartmentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function __construct(
        protected DepartmentService $DepartmentService,
    ) {}
    public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->input('search');
        $departments = $this->DepartmentService->getDepartment($perPage, $search);
        $colleges = College::all();

        return Inertia::render('Department', [
            'departments' => $departments,
            'colleges' => $colleges,
        ]);
    }

    public function store(DepartmentRequest $request)
    {
        Department::create($request->validated());

        return redirect()->back()->with('success', 'Department created successfully.');
    }

    public function update(UpdateBuildingRequest $request, Building $building)
    {
        $building->update($request->validated());

        return redirect()->back()->with('success', 'Building updated successfully.');
    }

    public function destroy(Building $building)
    {
        $building->delete();

        return redirect()
            ->back()
            ->with('success', 'Building deleted successfully.');
    }
}
