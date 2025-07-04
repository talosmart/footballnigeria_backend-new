<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;

Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']); 
Route::post('/forgot_password', [AuthController::class, 'forgot_password']); 
Route::post('/reset_password', [AuthController::class, 'reset_password']); 