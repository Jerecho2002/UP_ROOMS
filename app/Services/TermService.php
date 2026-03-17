<?php

namespace App\Services;

use App\Models\Term;

class TermService
{
    public function getTerms(int $perPage = 10, ?string $search = null)
    {
        return Term::orderByDesc('created_at')
            ->when($search, fn($query) => $query->where('term_name', 'like', "%{$search}%"))
            ->paginate($perPage)
            ->withQueryString();
    }
}
