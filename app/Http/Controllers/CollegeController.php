<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCollegeRequest;
use App\Http\Requests\UpdateCollegeRequest;
use App\Models\Building;
use App\Models\College;
use App\Models\UserAccount;
use App\Services\CollegeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollegeController extends Controller
{
    public function __construct(
        protected CollegeService $collegeService,
    ) {}
    public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->input('search');
        $colleges = $this->collegeService->getColleges($perPage, $search);
        $deans = UserAccount::all();

        return Inertia::render('CollegeDashboard', [
            'colleges' => $colleges,
            'deans' => $deans,
            'buildings' => Building::select('id', 'building_name')->get(),
        ]);
    }

    public function store(StoreCollegeRequest $request)
    {
        $validated = $request->validated();

        $college = College::create($validated);

        if (isset($validated['building_ids'])) {
            $college->buildings()->sync($validated['building_ids']);
        }

        return redirect()->back()->with('success', 'College created successfully.');
    }

    public function update(UpdateCollegeRequest $request, College $college)
    {
        $validated = $request->validated();

        $college->update($validated);

        $college->buildings()->sync($validated['building_ids'] ?? []);

        return redirect()->back()->with('success', 'College updated successfully.');
    }

    public function destroy(College $college)
    {
        $college->delete();

        return redirect()
            ->back()
            ->with('success', 'College deleted successfully.');
    }
}
