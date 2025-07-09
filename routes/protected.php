<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Blog\CategoryController;
use App\Http\Controllers\Blog\CommentController;
use App\Http\Controllers\Blog\LikeController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


Route::group(['middleware' => ['auth:sanctum']], function () {
    // Resend verification
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Already verified']);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent']);
    })->name('verification.send');

    Route::get('/email/verify', function () {
        return response()->json(['message' => 'Kindly verify your email']);;
    })->name('verification.notice');

    // Verify email link callback
    Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $request->fulfill();

        return response()->json(['message' => 'Email verified successfully.']);
    })->middleware(['signed'])->name('verification.verify');
});

// protected users routes
Route::group(['middleware' => ['auth:sanctum', 'role:user', 'verified']], function () {
    Route::post('/blog/comment/create-comment', [CommentController::class, 'create_comment']);
    Route::put('/blog/comment/update-comment', [CommentController::class, 'update_comment']);
    Route::post('/blog/likes/like-and-unlike-post', [LikeController::class, 'like_and_unlike_post']);
});

// Private admin routes
Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
    Route::post('/blog/create-blog', [BlogController::class, 'create_blog']);
    Route::put('/blog/update-blog', [BlogController::class, 'update_blog']);
    Route::delete('/blog/delete-blog', [BlogController::class, 'delete_blog']);
    Route::post('/blog/category/create-category', [CategoryController::class, 'create_category']);
    Route::get('/blog/category/get-category', [CategoryController::class, 'get_category']);
    Route::put('/blog/category/update-category', [CategoryController::class, 'update_category']);
    Route::delete('/blog/category/delete-category', [CategoryController::class, 'delete_category']);
    Route::put('/blog/comment/comment-approval', [CommentController::class, 'update_comment_approval']);
    Route::delete('/blog/comment/delete-comment', [CommentController::class, 'delete_comment']);
});

// Private admin & users routes
Route::group(['middleware' => ['auth:sanctum', 'role:admin,user']], function () {
    Route::post('/refresh', [AuthController::class, 'refresh']); 
    Route::post('/logout', [AuthController::class, 'logout']); 
});