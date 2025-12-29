<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainDashboardController;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Main Dashboard
    Route::get('/MainDashboard', [MainDashboardController::class, 'index'])
        ->name('main.dashboard')
        ->middleware('role:Admin,SYSADMIN,AO,ADPD,OCS');

    // Building Management
    Route::get('/BuildingDashboard', function () {
        return Inertia::render('BuildingDashboard');
    })->middleware('role:Admin,SYSADMIN,AO,ADPD');

    // College Management
    Route::get('/CollegeDashboard', function () {
        return Inertia::render('CollegeDashboard');
    })->middleware('role:Admin,SYSADMIN,AO,ADPD');

    // User Account Management
    Route::get('/UserAccountPage', function () {
        return Inertia::render('UserAccountPage');
    })->middleware('role:Admin,SYSADMIN');

    // Department Management
    Route::get('/Department', function () {
        return Inertia::render('Department');
    })->middleware('role:Admin,SYSADMIN,AO,ADPD');

    // Equipment Management
    Route::get('/equipment', function () {
        return Inertia::render('Equipment');
    })->middleware('role:Admin,SYSADMIN,AO');

    // Room Types
    Route::get('/roomtypes', function () {
        return Inertia::render('RoomTypes');
    })->middleware('role:Admin,SYSADMIN');

    // Room Booking
    Route::get('/room', function () {
        return Inertia::render('Room');
    })->middleware('role:Admin,SYSADMIN,Faculty,Staff');

    // Schedule
    Route::get('/schedule', function () {
        return Inertia::render('Schedule');
    })->middleware('role:Admin,SYSADMIN,Staff,Faculty,USER,DPTAPR');

    // Terms
    Route::get('/Terms', function () {
        return Inertia::render('Terms');
    });
});
