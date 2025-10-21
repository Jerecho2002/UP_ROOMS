<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BuildingController; // <-- Ensure this is used
use App\Http\Controllers\MainDashboardController;
use App\Http\Controllers\CollegeDashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Building;


// Existing Routes
Route::get('/MainDashboard', function () {
    return Inertia::render('MainDashboard');
});

// **********************************************
// UPDATED: Route for Building Dashboard
// This now uses the BuildingController to fetch data and render the BuildingsPage
// **********************************************
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
// <script setup>... rest of content is frontend and irrelevant in this PHP file

// (Assuming your authentication routes are defined elsewhere)
