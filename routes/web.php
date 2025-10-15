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



Route::get('/MainDashboard', function () {
    return Inertia::render('MainDashboard');
});




Route::get('/BuildingDashboard', function () {
    return Inertia::render('BuildingDashboard');
});

Route::get('/Terms',  function(){
    return Inertia::render('Terms');
});


Route::get('/CollegeDashboard', function () {
    return Inertia::render('CollegeDashboard');
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

Route::get('/RoomTypes', function () {
    return Inertia::render('RoomTypes');
});

Route::get('/room', function () {
    return Inertia::render('room');
});


Route::get('/Schedule', function () {
    return Inertia::render('Schedule');
});
/*
|------------------------------------------------------QA--------------------
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
