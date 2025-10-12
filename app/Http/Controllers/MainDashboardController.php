<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class MainDashboardController extends Controller
{
    public function index()
    {
        $totalAccounts = 5;
        $totalDepartments = 3;
        $totalColleges = 4;
        $totalRooms = 10;

        $rooms = [
            ['id'=>1, 'room_name'=>'Room 101', 'building'=>'Admin Building', 'college'=>'College of Business Admin', 'capacity'=>40, 'location'=>'1st Floor, North', 'room_type'=>'Lecture'],
            ['id'=>2, 'room_name'=>'Room 102', 'building'=>'Science Building', 'college'=>'College of Science', 'capacity'=>50, 'location'=>'2nd Floor, West', 'room_type'=>'Lab'],
            ['id'=>3, 'room_name'=>'Room 201', 'building'=>'Engineering Building', 'college'=>'College of Engineering', 'capacity'=>60, 'location'=>'2nd Floor, South', 'room_type'=>'Computer Lab'],
            ['id'=>4, 'room_name'=>'Room 202', 'building'=>'Education Building', 'college'=>'College of Education', 'capacity'=>45, 'location'=>'3rd Floor, East', 'room_type'=>'Lecture'],
            ['id'=>5, 'room_name'=>'Room 301', 'building'=>'Law Building', 'college'=>'College of Law', 'capacity'=>35, 'location'=>'3rd Floor, West', 'room_type'=>'Seminar'],
        ];

        return Inertia::render('MainAccount', [
            'totalAccounts' => $totalAccounts,
            'totalDepartments' => $totalDepartments,
            'totalColleges' => $totalColleges,
            'totalRooms' => $totalRooms,
            'rooms' => $rooms,
        ]);
    }
}
