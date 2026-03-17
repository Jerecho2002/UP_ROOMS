<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\College;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Schedule;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'view']);
        Permission::create(['name' => 'update']);
        Permission::create(['name' => 'delete']);

        $adminRole = Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());
        $staffRole = Role::create(['name' => 'staff'])->givePermissionTo(['view', 'update']);
        $userRole  = Role::create(['name' => 'user'])->givePermissionTo(['view']);

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('admin');

        $staff = User::factory()->create([
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
        ])->assignRole('staff');

        // 2. Create random users
        $users = User::factory()->count(10)->create()->each(function ($user) {
            $user->assignRole('user');
        });

        // 3. Now assign colleges/departments to accounts AFTER accounts exist
        $colleges = College::factory()->count(4)->create();
        $departments = Department::factory()->count(6)->create();

        foreach ($users as $user) {
            $user->userAccount->update([
                'college_id' => $colleges->random()->id,
                'department_id' => $departments->random()->id,
            ]);
        }

        foreach ($colleges as $college) {
            $userAccount = $users->random()->userAccount;
            $college->update(['dean_id' => $userAccount->id]);
        }

        foreach ($departments as $department) {
            $userAccount = $users->random()->userAccount;
            $department->update(['department_head_id' => $userAccount->id]);
        }

        // ------------------------------------------------------------------
        // 6. Other entities
        // ------------------------------------------------------------------
        Building::factory()->count(5)->create();
        RoomType::factory()->count(4)->create();
        Room::factory()->count(10)->create();
        Equipment::factory()->count(20)->create();

        $terms = Term::factory()->count(3)->create();
        if ($terms->isNotEmpty()) {
            $terms->first()->update([
                'is_current' => true,
                'status' => 'active',
            ]);
        }

        Schedule::factory()->count(20)->create();
    }
}
