<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class BuildingService
{
    public function getAllBuildings($search = null)
    {
        $query = Building::with(['college:id,college_name,college_code'])
            ->withCount(['rooms', 'equipment'])
            ->orderBy('building_name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('building_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('college', function ($q) use ($search) {
                      $q->where('college_name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->paginate(10);
    }

    public function getBuildingStats($buildingId)
    {
        $building = Building::findOrFail($buildingId);

        return [
            'total_rooms' => $building->rooms()->count(),
            'occupied_rooms' => $building->rooms()->where('status', 'occupied')->count(),
            'available_rooms' => $building->rooms()->where('status', 'available')->count(),
            'under_maintenance' => $building->rooms()->where('status', 'maintenance')->count(),
            'total_equipment' => $building->equipment()->count(),
            'available_equipment' => $building->equipment()->where('status', 'available')->count(),
            'in_use_equipment' => $building->equipment()->where('status', 'in_use')->count(),
        ];
    }

    public function createBuilding($data)
    {
        return Building::create($data);
    }

    public function updateBuilding($buildingId, $data)
    {
        $building = Building::findOrFail($buildingId);
        $building->update($data);
        return $building;
    }

    public function deleteBuilding($buildingId)
    {
        $building = Building::findOrFail($buildingId);
        $building->delete();
        return true;
    }
}
