<?php

namespace App\Services;

use App\Models\College;
use App\Models\Department;
use App\Models\UserAccount;
use Illuminate\Support\Facades\DB;

class CollegeService
{
    public function getAllColleges($search = null)
    {
        $query = College::with(['dean:id,first_name,last_name,username'])
            ->withCount(['departments', 'buildings', 'rooms', 'userAccounts'])
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

        return $query->paginate(10);
    }

    public function getCollegeStats($collegeId)
    {
        $college = College::findOrFail($collegeId);

        return [
            'total_departments' => $college->departments()->count(),
            'total_buildings' => $college->buildings()->count(),
            'total_rooms' => $college->rooms()->count(),
            'total_staff' => $college->userAccounts()->count(),
            'total_equipment' => $college->equipment()->count(),
            'departments' => $college->departments()->select('department_name')->get(),
        ];
    }

    public function createCollege($data)
    {
        return College::create($data);
    }

    public function updateCollege($collegeId, $data)
    {
        $college = College::findOrFail($collegeId);
        $college->update($data);
        return $college;
    }

    public function deleteCollege($collegeId)
    {
        $college = College::findOrFail($collegeId);
        $college->delete();
        return true;
    }
}
