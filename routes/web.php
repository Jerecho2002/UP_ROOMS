<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MainDashboardController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\DashboardController;

// Simple auth check function
function authCheck(Request $request) {
    return $request->session()->has('user');
}

// Login Routes
Route::get('/login', function () {
    if (authCheck(request())) {
        return redirect('/MainDashboard');
    }
    return Inertia::render('Login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public API routes (for debugging)
Route::get('/api/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => [
            'connected' => \DB::connection()->getPdo() ? true : false,
            'name' => \DB::connection()->getDatabaseName()
        ]
    ]);
});

// Protected routes with auth check
Route::middleware(['auth.session'])->group(function () {
    // Main Dashboard (with pagination and search)
    Route::get('/', [MainDashboardController::class, 'index'])->name('dashboard');
    Route::get('/MainDashboard', [MainDashboardController::class, 'index'])->name('main.dashboard');

    // API endpoints for frontend
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/api/dashboard/rooms', [DashboardController::class, 'getRooms']);
    Route::get('/api/dashboard/search', [DashboardController::class, 'search']);

    // Building Management
    Route::get('/BuildingDashboard', [BuildingController::class, 'index'])->name('buildings.index');
    Route::post('/buildings', [BuildingController::class, 'store']);
    Route::put('/buildings/{building}', [BuildingController::class, 'update']);
    Route::delete('/buildings/{building}', [BuildingController::class, 'destroy']);

    // College Management
    Route::get('/CollegeDashboard', [CollegeController::class, 'index'])->name('colleges.index');
    Route::get('/api/colleges', [CollegeController::class, 'getAll']);
    Route::get('/api/colleges/stats/{college}', [CollegeController::class, 'getStats']);
    Route::post('/colleges', [CollegeController::class, 'store']);
    Route::put('/colleges/{college}', [CollegeController::class, 'update']);
    Route::delete('/colleges/{college}', [CollegeController::class, 'destroy']);

    // Department Management
    Route::get('/Department', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('/api/departments', [DepartmentController::class, 'getAll']);
    Route::get('/api/departments/stats/{department}', [DepartmentController::class, 'getStats']);
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::put('/departments/{department}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);

    // Room Types
    Route::get('/roomtypes', [RoomTypeController::class, 'index'])->name('roomtypes.index');
    Route::get('/api/room-types', [RoomTypeController::class, 'getAll']);
    Route::post('/room-types', [RoomTypeController::class, 'store']);
    Route::put('/room-types/{roomType}', [RoomTypeController::class, 'update']);
    Route::delete('/room-types/{roomType}', [RoomTypeController::class, 'destroy']);

    // Rooms Management
    Route::get('/room', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/api/rooms', [RoomController::class, 'getAll']);
    Route::get('/api/rooms/{id}', [RoomController::class, 'show']);
    Route::get('/api/rooms/{id}/availability', [RoomController::class, 'getAvailability']);
    Route::post('/rooms', [RoomController::class, 'store']);
    Route::put('/rooms/{room}', [RoomController::class, 'update']);
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);

    // Equipment Management
    // In web.php, make sure this route exists and returns Inertia::render()

  // Equipment Management Routes
Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');

// Equipment API Routes
Route::prefix('/api/equipment')->group(function () {
    Route::get('/', [EquipmentController::class, 'getAll']);
    Route::get('/stats', [EquipmentController::class, 'getStats']);
    Route::get('/usage', [EquipmentController::class, 'getEquipmentUsage']);
    Route::post('/', [EquipmentController::class, 'store']);
    Route::put('/{equipment}', [EquipmentController::class, 'update']);
    Route::post('/{equipment}/transfer', [EquipmentController::class, 'transfer']);
    Route::delete('/{equipment}', [EquipmentController::class, 'destroy']);
});
    // Schedule Management
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/api/schedules', [ScheduleController::class, 'getAll']);
    Route::get('/api/schedules/stats', [ScheduleController::class, 'getStats']);
    Route::get('/api/schedules/check-availability', [ScheduleController::class, 'checkAvailability']);
    Route::get('/api/schedules/{id}/approve', [ScheduleController::class, 'approve']);
    Route::get('/api/rooms/{roomId}/schedules', [ScheduleController::class, 'getRoomSchedules']);
    Route::post('/schedules', [ScheduleController::class, 'store']);
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update']);
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy']);

    // Terms Management
    Route::get('/Terms', [TermController::class, 'index'])->name('terms.index');
    Route::get('/api/terms', [TermController::class, 'getAll']);
    Route::get('/api/terms/current', [TermController::class, 'getCurrent']);
    Route::get('/api/terms/{id}/stats', [TermController::class, 'getStats']);
    Route::get('/api/terms/{id}/set-current', [TermController::class, 'setCurrent']);
    Route::get('/api/terms/{id}/calendar', [TermController::class, 'getCalendar']);
    Route::post('/terms', [TermController::class, 'store']);
    Route::put('/terms/{term}', [TermController::class, 'update']);
    Route::delete('/terms/{term}', [TermController::class, 'destroy']);

    // User Account Management
   // User Account Management

Route::get('/UserAccountPage', [UserAccountController::class, 'index'])->name('user-accounts.index');
Route::post('/user-accounts', [UserAccountController::class, 'store'])->name('user-accounts.store');
Route::put('/user-accounts/{userAccount}', [UserAccountController::class, 'update'])->name('user-accounts.update');
Route::delete('/user-accounts/{userAccount}', [UserAccountController::class, 'destroy'])->name('user-accounts.destroy');
Route::post('/user-accounts/{userAccount}/change-status', [UserAccountController::class, 'changeStatus'])->name('user-accounts.change-status');
Route::post('/user-accounts/bulk-actions', [UserAccountController::class, 'bulkActions'])->name('user-accounts.bulk-actions');
    // Report Generation
    Route::prefix('/api/reports')->group(function () {
        Route::get('/room-utilization', function (Request $request) {
            return app(\App\Services\ReportService::class)->generateRoomUtilizationReport(
                $request->query('start_date', now()->subDays(30)->format('Y-m-d')),
                $request->query('end_date', now()->format('Y-m-d'))
            );
        });

        Route::get('/equipment-status', function () {
            return app(\App\Services\ReportService::class)->generateEquipmentStatusReport();
        });

        Route::get('/user-activity', function (Request $request) {
            return app(\App\Services\ReportService::class)->generateUserActivityReport(
                $request->query('start_date', now()->subDays(30)->format('Y-m-d')),
                $request->query('end_date', now()->format('Y-m-d'))
            );
        });

        Route::get('/schedule-report', function (Request $request) {
            return app(\App\Services\ReportService::class)->generateScheduleReport(
                $request->query('start_date', now()->subDays(30)->format('Y-m-d')),
                $request->query('end_date', now()->format('Y-m-d'))
            );
        });

        Route::get('/building-report', function () {
            return app(\App\Services\ReportService::class)->generateBuildingReport();
        });
    });
});

// **PROBLEM**: You have duplicate routes defined for the same paths
// Remove the duplicate fallback routes section below, OR
// Make sure the controller routes above are properly rendering Inertia components

// Alternative solution: Keep both but ensure controllers return Inertia::render()
// Here's what each controller's index() method should return:

/*
// In EquipmentController::index()
public function index()
{
    return Inertia::render('Equipment');
}

// In RoomController::index()
public function index()
{
    return Inertia::render('Room');
}

// In RoomTypeController::index()
public function index()
{
    return Inertia::render('RoomTypes');
}

// In ScheduleController::index()
public function index()
{
    return Inertia::render('Schedule');
}
*/

// Catch-all route for SPA (Single Page Application) - should be LAST
Route::get('/{any}', function () {
    return Inertia::render('NotFound');
})->where('any', '.*');
