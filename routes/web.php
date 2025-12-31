<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;

// Login Routes
Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Simple auth check function
function authCheck(Request $request) {
    return $request->session()->has('user');
}

// Protected routes with inline auth check
Route::get('/', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    return Inertia::render('MainDashboard');
});

Route::get('/MainDashboard', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    return Inertia::render('MainDashboard');
});

Route::get('/BuildingDashboard', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    return Inertia::render('BuildingDashboard');
});

Route::get('/Terms', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    return Inertia::render('Terms');
});

Route::get('/CollegeDashboard', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    return Inertia::render('CollegeDashboard');
});

Route::get('/UserAccountPage', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    $users = \App\Models\UserAccount::all();
    return Inertia::render('UserAccountPage', [
        'initialUsers' => $users
    ]);
});

Route::get('/Department', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    return Inertia::render('Department');
});

Route::get('/equipment', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    return Inertia::render('equipment');
});

Route::get('/roomtypes', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    return Inertia::render('roomtypes');
});

Route::get('/room', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    return Inertia::render('room');
});

Route::get('/schedule', function (Request $request) {
    if (!authCheck($request)) {
        return redirect('/login');
    }
    return Inertia::render('schedule');
});
