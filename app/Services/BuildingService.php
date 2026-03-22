<?php

namespace App\Services;

use App\Models\Building;

class BuildingService
{
    public function getBuildings(int $perPage = 10, ?string $search = null)
    {
        return Building::with('colleges:id')
            ->orderByDesc('created_at')
            ->when($search, fn($query) => $query->where('building_name', 'like', "%{$search}%"))
            ->paginate($perPage)
            ->through(fn($building) => [
                ...$building->toArray(),
                'college_ids'  => $building->colleges->pluck('id')->toArray(),
                'has_elevator' => (int) $building->has_elevator,
                'has_parking'  => (int) $building->has_parking,
            ])
            ->withQueryString();
    }
}
