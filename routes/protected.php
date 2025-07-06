<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Blog\CategoryController;
use App\Http\Controllers\Blog\CommentController;
use App\Http\Controllers\Blog\LikeController;

// protected users routes
Route::middleware(['auth:sanctum', 'role:user', 'verified'])->group(function () {
    Route::post('/blog/comment/create-comment', [CommentController::class, 'create_comment']);
    Route::put('/blog/comment/update-comment', [CommentController::class, 'update_comment']);
    Route::post('/blog/likes/like-and-unlike-post', [LikeController::class, 'like_and_unlike_post']);
});

// Private admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/blog/create-blog', [BlogController::class, 'create_blog']);
    Route::put('/blog/update-blog', [BlogController::class, 'update_blog']);
    Route::delete('/blog/delete-blog', [BlogController::class, 'delete_blog']);
    Route::post('/blog/category/category-approval', [CategoryController::class, 'create_category']);
    Route::get('/blog/category/get-category', [CategoryController::class, 'get_category']);
    Route::put('/blog/category/update-category', [CategoryController::class, 'update_category']);
    Route::delete('/blog/category/delete-category', [CategoryController::class, 'delete_category']);
    Route::put('/blog/comment/comment-approval', [CommentController::class, 'update_comment_approval']);
    Route::delete('/blog/comment/delete-comment', [CommentController::class, 'delete_comment']);
});

// Private admin & users routes
Route::middleware(['auth:sanctum', 'role:admin, user'])->group(function () {
    Route::post('/refresh', [AuthController::class, 'refresh']); 
    Route::post('/logout', [AuthController::class, 'logout']); 
});