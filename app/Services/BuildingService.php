<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class BuildingService
{
   public function getBuildings(int $perPage = 10, ?string $search = null)
    {
        return Building::with('college')
            ->orderByDesc('created_at')
            ->when($search, fn($query) => $query->where('building_name', 'like', "%{$search}%"))
            ->paginate($perPage)
            ->withQueryString();
    }
}
