<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class UserAccountController extends Controller
{
    public function index()
    {
        $users = [
            ['name' => 'John Doe', 'school' => 'UP Cebu', 'age' => 22, 'address' => 'Cebu City', 'room' => '101', 'start' => '8:00 AM', 'end' => '10:00 AM'],
            ['name' => 'Jane Smith', 'school' => 'UP Cebu', 'age' => 20, 'address' => 'Mandaue', 'room' => '102', 'start' => '10:00 AM', 'end' => '12:00 PM'],
            ['name' => 'Mike Cruz', 'school' => 'UP Cebu', 'age' => 23, 'address' => 'Talisay', 'room' => '103', 'start' => '1:00 PM', 'end' => '3:00 PM'],
        ];

        return Inertia::render('UserAccountPage', [
            'users' => $users
        ]);

//  return Inertia::render('building_dashboard', [
//             'buildings' => $buildings
//         ]);
//     }
    }


    }

