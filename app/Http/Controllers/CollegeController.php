<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CollegeController extends Controller
{
    public function index()
    {
        return Inertia::render('CollegeDashboard');
    }

    public function getAll(Request $request)
    {
        $search = $request->query('search');
        $query = College::with(['dean:id,first_name,last_name,username'])
            ->orderBy('college_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('college_name', 'like', "%{$search}%")
                  ->orWhere('college_code', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%")
                  ->orWhereHas('dean', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'college_name' => 'required|string|max:150|unique:colleges',
            'college_code' => 'nullable|string|max:50|unique:colleges',
            'description' => 'nullable|string',
            'dean_id' => 'nullable|exists:user_accounts,id',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        $college = College::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'College created successfully',
            'data' => $college->load('dean')
        ], 201);
    }

    public function update(Request $request, College $college)
    {
        $validated = $request->validate([
            'college_name' => 'required|string|max:150|unique:colleges,college_name,' . $college->id,
            'college_code' => 'nullable|string|max:50|unique:colleges,college_code,' . $college->id,
            'description' => 'nullable|string',
            'dean_id' => 'nullable|exists:user_accounts,id',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
        ]);

        $college->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'College updated successfully',
            'data' => $college->load('dean')
        ]);
    }

    public function destroy(College $college)
    {
        $college->delete();

        return response()->json([
            'success' => true,
            'message' => 'College deleted successfully'
        ]);
    }
}
