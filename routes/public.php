<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

//public routes
Route::post('/login', [AuthController::class, 'login']); 