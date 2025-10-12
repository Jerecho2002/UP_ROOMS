<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use App\Models\Building;

class Collegecontroller extends Controller
{
    public function index()
    {
        // Example: counting records
        $classCount = Room::count();
        $studentCount = User::where('role', 'student')->count();
        $teacherCount = User::where('role', 'teacher')->count();
        $messageCount = 0; // You can connect to messages table later

        // Example Events / News (dummy for now)
        $events = [
            ['title' => 'Orientation', 'date' => '2025-10-10'],
            ['title' => 'Sports Fest', 'date' => '2025-11-01'],
        ];

        $leaderboard = [
            ['name' => 'John Doe', 'points' => 120],
            ['name' => 'Jane Smith', 'points' => 110],
        ];

        return view('college_dashboard', compact(
            'classCount',
            'studentCount',
            'teacherCount',
            'messageCount',
            'events',
            'leaderboard'
        ));
    }
}
