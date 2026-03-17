<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTermRequest;
use App\Http\Requests\UpdateTermRequest;
use App\Models\Term;
use App\Services\TermService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TermController extends Controller
{
    public function __construct(
        protected TermService $termService,
    ) {}

    public function index(Request $request)
    {
        $perPage = 10;
        $search = $request->input('search');

        $terms = $this->termService->getTerms($perPage, $search);

        return Inertia::render('Terms', [
            'terms' => $terms,
        ]);
    }

    public function store(StoreTermRequest $request)
    {
        try {
            Term::create($request->validated());

            return redirect()
                ->back()
                ->with('success', 'Term created successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to create term. Please try again.');
        }
    }

    public function update(UpdateTermRequest $request, Term $term)
    {
        try {
            $term->update($request->validated());

            return redirect()
                ->back()
                ->with('success', 'Term updated successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update term. Please try again.');
        }
    }

    public function destroy(Term $term)
    {
        try {
            $term->delete();

            return redirect()
                ->back()
                ->with('success', 'Term deleted successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete term. It may be in use.');
        }
    }
}
