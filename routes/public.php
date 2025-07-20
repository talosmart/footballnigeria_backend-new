<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\PostApiController;

Route::group(['prefix' => 'auth'], function(){
    Route::post('/register', [AuthController::class, 'register']); 
    Route::post('/login', [AuthController::class, 'login']); 
});

Route::group(['prefix' => 'password'], function(){
    Route::post('/forgot-password', [AuthController::class, 'forgot_password'])->name('password.email'); 
    Route::post('/reset-password', [AuthController::class, 'reset_password'])->name('password.update'); 
});

Route::get('/categories',[PostApiController::class,'category']);

Route::group(['prefix' => 'blog'], function(){
    Route::get('list',[PostApiController::class,'listPost']);
    Route::get('single',[PostApiController::class,'singlePost']);
});

require __DIR__.'/thirdPartyApi.php';