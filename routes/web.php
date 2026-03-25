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

Route::get('/login', [LoginController::class, 'showLogin'])
    ->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:admin'])->group(function () {

        Route::resource('MainDashboard', MainDashboardController::class)
            ->except(['create', 'edit', 'show'])
            ->parameters(['MainDashboard' => 'mainDashboard']);

        Route::resource('BuildingDashboard', BuildingController::class)
            ->except(['create', 'edit', 'show'])
            ->parameters(['BuildingDashboard' => 'building']);

        Route::resource('CollegeDashboard', CollegeController::class)
            ->except(['create', 'edit', 'show'])
            ->parameters(['CollegeDashboard' => 'college']);

        Route::resource('Departments', DepartmentController::class)
            ->except(['create', 'edit', 'show'])
            ->parameters(['Departments' => 'department']);

        Route::resource('RoomTypes', RoomTypeController::class)
            ->except(['create', 'edit', 'show'])
            ->parameters(['RoomTypes' => 'roomtype']);

        Route::resource('Rooms', RoomController::class)
            ->except(['create', 'edit', 'show'])
            ->parameters(['Rooms' => 'room']);

        Route::resource('UserAccounts', UserAccountController::class)
            ->except(['create', 'edit', 'show'])
            ->parameters(['UserAccounts' => 'userAccount']);

        Route::resource('Terms', TermController::class)
            ->except(['create', 'edit', 'show'])
            ->parameters(['Terms' => 'term']);

        Route::resource('Equipment', EquipmentController::class)
            ->except(['create', 'edit', 'show'])
            ->parameters(['Equipment' => 'equipment']);

        Route::get('/Schedule', [ScheduleController::class, 'index'])->name('schedules.index');

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
});

Route::get('/{any}', function () {
    return Inertia::render('NotFound');
})->where('any', '.*');
