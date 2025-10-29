<?php

namespace App\Services;
use App\Models\Room;
class MainDashboardService
{
    public function getMainDashboard($search = null){
        return Room::with("college", "userAccount", "schedules")
        ->when($search, function($q) use ($search){
            $q->where('room_name', 'like', "%{$search}%");
        })
        ->paginate(10)
        ->withQueryString();
    }
}