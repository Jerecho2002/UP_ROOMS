<?php

namespace App\Http\Controllers;

use App\Models\Term;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TermController extends Controller
{
    public function index()
    {
        return Inertia::render('Terms');
    }

    public function getAll(Request $request)
    {
        $search = $request->query('search');
        $query = Term::orderBy('academic_year', 'desc')
            ->orderBy('start_date', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('term_name', 'like', "%{$search}%")
                  ->orWhere('term_code', 'like', "%{$search}%")
                  ->orWhere('academic_year', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'term_name' => 'required|string|max:100|unique:terms',
            'term_code' => 'required|string|max:50|unique:terms',
            'term_type' => 'required|in:semester,trimester,quarter,summer,special',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'enrollment_start' => 'nullable|date',
            'enrollment_end' => 'nullable|date|after_or_equal:enrollment_start',
            'classes_start' => 'required|date',
            'classes_end' => 'required|date|after_or_equal:classes_start',
            'examination_start' => 'nullable|date',
            'examination_end' => 'nullable|date|after_or_equal:examination_start',
            'is_current' => 'boolean',
            'status' => 'required|in:upcoming,active,completed,cancelled',
            'academic_year' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        // If this term is set as current, unset any other current term
        if ($validated['is_current']) {
            Term::where('is_current', true)->update(['is_current' => false]);
        }

        $term = Term::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Term created successfully',
            'data' => $term
        ], 201);
    }

    public function update(Request $request, Term $term)
    {
        $validated = $request->validate([
            'term_name' => 'required|string|max:100|unique:terms,term_name,' . $term->id,
            'term_code' => 'required|string|max:50|unique:terms,term_code,' . $term->id,
            'term_type' => 'required|in:semester,trimester,quarter,summer,special',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'enrollment_start' => 'nullable|date',
            'enrollment_end' => 'nullable|date|after_or_equal:enrollment_start',
            'classes_start' => 'required|date',
            'classes_end' => 'required|date|after_or_equal:classes_start',
            'examination_start' => 'nullable|date',
            'examination_end' => 'nullable|date|after_or_equal:examination_start',
            'is_current' => 'boolean',
            'status' => 'required|in:upcoming,active,completed,cancelled',
            'academic_year' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        // If this term is set as current, unset any other current term
        if ($validated['is_current']) {
            Term::where('id', '!=', $term->id)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        $term->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Term updated successfully',
            'data' => $term
        ]);
    }

    public function destroy(Term $term)
    {
        $term->delete();

        return response()->json([
            'success' => true,
            'message' => 'Term deleted successfully'
        ]);
    }
}
