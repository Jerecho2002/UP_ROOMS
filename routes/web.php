<?php
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainDashboardController;

// Existing Routes
Route::get('/MainDashboard', [MainDashboardController::class, 'index'])->name('main.index');

Route::get('/BuildingDashboard', function(){
    return Inertia::render('BuildingDashboard');
});

Route::get('/Terms', function(){
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

Route::get('/roomtypes', function () {
    return Inertia::render('roomtypes');
});

Route::get('/room', function () {
    return Inertia::render('room');
});


Route::get('/schedule', function () {
    return Inertia::render('schedule');
});





