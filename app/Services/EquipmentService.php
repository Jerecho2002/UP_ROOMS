<?php

namespace App\Services;

use App\Models\Equipment;

class EquipmentService
{
    public function getEquipment(int $perPage = 10, ?string $search = null)
    {
        return Equipment::with('building')
            ->orderByDesc('created_at')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('building', function ($q) use ($search) {
                    $q->where('building_name', 'like', "%{$search}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString();
    }
}
