<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainDashboardController;
use App\Http\Controllers\UserAccountController; // Add this

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

// User Account Page with data
Route::get('/UserAccountPage', function () {
    $users = \App\Models\UserAccount::all();
    return Inertia::render('UserAccountPage', [
        'initialUsers' => $users
    ]);
});

// API Routes for User Accounts
Route::prefix('user-accounts')->group(function () {
    Route::get('/', [UserAccountController::class, 'index']);
    Route::post('/', [UserAccountController::class, 'store']);
    Route::put('/{userAccount}', [UserAccountController::class, 'update']);
    Route::delete('/{userAccount}', [UserAccountController::class, 'destroy']);
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

Route::get('/login', function () {
    return Inertia::render('login');
});
