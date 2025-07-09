<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Blog\BlogController;

Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']); 
Route::post('/forgot-password', [AuthController::class, 'forgot_password'])->name('password.email'); 
Route::post('/reset-password', [AuthController::class, 'reset_password'])->name('password.update'); 
Route::get('/blog/get-blog', [BlogController::class, 'get_blog']);

require __DIR__.'/thirdPartyApi.php';