<?php

use App\Http\Controllers\UserAccountController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () { // Or 'auth:api' if using API tokens
    Route::apiResource('user-accounts', UserAccountController::class);
});
