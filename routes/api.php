<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__.'/public.php';
require __DIR__.'/protected.php';

Route::get('password/reset/{token}', function ($token) {
    return response()->json(['token' => $token]);
})->name('password.reset');