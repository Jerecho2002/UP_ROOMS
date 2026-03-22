<?php

namespace App\Services;

use App\Models\College;

class CollegeService
{
    public function getColleges(int $perPage = 10, ?string $search = null)
    {
        return College::with(['dean', 'buildings:id'])
            ->orderByDesc('created_at')
            ->when($search, fn($query) => $query->where('college_name', 'like', "%{$search}%"))
            ->paginate($perPage)
            ->through(fn($college) => [
                ...$college->toArray(),
                'building_ids' => $college->buildings->pluck('id')->toArray(),
                'dean_name'    => $college->dean?->name ?? 'N/A',
                'departments' => $college->departments->pluck('department_name')->toArray(),
            ])
            ->withQueryString();
    }
}
