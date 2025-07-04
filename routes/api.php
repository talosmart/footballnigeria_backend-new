<?php

require __DIR__.'/public.php';
require __DIR__.'/protected.php';


// email verification routes
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Verify the email (user clicks this link from email)
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); 
    return response()->json(['message' => 'Email verified!']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

// Resend the email
Route::post('/email/resend', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Already verified.']);
    }

    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent.']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');