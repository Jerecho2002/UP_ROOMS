<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\MainDashboardController;
use App\Http\Controllers\CollegeDashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
 use App\Models\Building;



Route::get('/MainAccount', function () {
    return Inertia::render('MainAccount');
});



Route::get('/building_dashboard', function () {
    return Inertia::render('building_dashboard');
});


Route::get('/college_dashboard', function () {
    return Inertia::render('college_dashboard');
});


Route::get('/UserAccountPage', function () {
    return Inertia::render('UserAccountPage');
});


Route::get('/Department', function () {
    return Inertia::render('Department');
});

Route::get('/equipment', function () {
    return Inertia::render('equipment');
});

Route::get('/roomtypes', function () {
    return Inertia::render('roomtypes');
});

Route::get('/room', function () {
    return Inertia::render('room');
});


Route::get('/schedule', function () {
    return Inertia::render('schedule');
});
/*
|--------------------------------------------------------------------------
| Authentication + Profile routes
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
