<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// protected users routes
Route::middleware(['auth:sanctum', 'role:user', 'verified'])->group(function () {
    // Route::get('/user', [AuthController::class, 'login']);
});


// Private admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Route::get('/user/dashboard', [UserDashboardController::class, 'index']);
});

// Private admin & users routes
Route::middleware(['auth:sanctum', 'role:admin, user'])->group(function () {
    Route::post('/refresh', [AuthController::class, 'refresh']); 
    Route::post('/logout', [AuthController::class, 'logout']); 
});