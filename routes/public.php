<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Blog\BlogController;

Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']); 
Route::post('/forgot-password', [AuthController::class, 'forgot_password']); 
Route::post('/reset-password', [AuthController::class, 'reset_password']); 
Route::post('/blog/get-blog', [BlogController::class, 'get_blog']);