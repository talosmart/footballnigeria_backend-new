<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Fan\FanPostController;
use App\Http\Controllers\Fan\FanCommentController;
use App\Http\Controllers\Fan\FanReactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostApiController;
use App\Http\Controllers\PollController; 
use App\Http\Controllers\TagController;

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

Route::group(['prefix' => 'fans'], function () {
    Route::group(['prefix' => 'topics'], function () {
        Route::group(['middleware' => ['auth:sanctum', 'role:user', 'verified']], function () {
            Route::post('/create', [FanPostController::class, 'createTopic']);
            Route::delete('/delete/{id}', [FanPostController::class, 'deleteTopic']);
            Route::put('/update/{id}', [FanPostController::class, 'updateTopic']);
            Route::get('/list/{status?}/{ownership?}', [FanPostController::class, 'ListTopic']);
        });
    });

    Route::group(['prefix' => 'post'], function () {
        Route::group(['middleware' => ['auth:sanctum', 'role:user', 'verified']], function () {    
            Route::get('/categories', [FanPostController::class, 'postcategories']);
            Route::post('/create', [FanPostController::class,'createPost']);
            Route::post('/update/{id}',[FanPostController::class,'updatePost']);
            Route::get('/list',[FanPostController::class,'listPosts']);
            Route::delete('/delete/{id}',[FanPostController::class,'deletePost']);
            Route::post('/{post_id}/react',[FanReactionController::class,'reactToPost']);
            Route::get('/{post_id}/get',[FanPostController::class,'getPost']);
            Route::post('/{post_id}/comment',[FanCommentController::class,'addComment']);
            Route::get('/{post_id}/list/comment',[FanCommentController::class,'listComments']);
            Route::post('/{post_id}/update/comment',[FanCommentController::class,'updateComment']);
            Route::get('/{comment_id}/get/comment',[FanCommentController::class,'getComment']);
            Route::delete('/{comment_id}/delete/comment',[FanCommentController::class,'deleteComment']);
            Route::post('/{comment_id}/comment/react',[FanReactionController::class,'reactToComment']);
        });

        Route::group(['middleware' => ['auth:sanctum', 'role:user', 'verified']], function () {     
            Route::post('/{comment_id}/reply',[FanCommentController::class,'addCommentReply']);
            Route::post('/{reply_id}/update/reply',[FanCommentController::class,'updateReply']);
            Route::get('/{reply_id}/get/reply',[FanCommentController::class,'getReply']);
            Route::get('/{reply_id}/delete/reply',[FanCommentController::class,'deleteReply']);
            Route::post('/{reply_id}/reply/react',[FanReactionController::class,'reactToReply']); 
        });
    });
});

Route::group(['prefix' => 'blog'], function(){
    Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
        Route::get('/category', [PostApiController::class, 'category']); 
        Route::post('/post_category', [PostApiController::class, 'createPostCategory']); 
        Route::post('/create_post', [PostApiController::class, 'createPost']);

        Route::post('/create-tag', [TagController::class, 'createTag']); 
        Route::get('/get-tag', [TagController::class, 'getTag']); 
        Route::put('/update-tag/{tagName}', [TagController::class, 'updateTag']);
        Route::delete('/delete-tag/{tagName}', [TagController::class, 'deleteTag']);
    });
});

Route::group(['prefix' => 'poll'], function(){
    Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
        Route::post('/create-poll', [PollController::class, 'createPoll']);
        Route::get('/get-poll', [PollController::class, 'getPoll']);
        Route::put('/update-poll', [PollController::class, 'updatePoll']);
        Route::delete('/delete-poll/{id}', [PollController::class, 'deletePoll']);
    });

    Route::group(['middleware' => ['auth:sanctum', 'role:user', 'verified']], function () {
        Route::post('/cast-vote', [PollController::class, 'pollCaster']);
    });
});

// Private admin & users routes
Route::group(['prefix' => 'auth'], function(){
    Route::group(['middleware' => ['auth:sanctum', 'role:admin,user']], function () {
        Route::get('/user', [UserController::class, 'user']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/profile/picture', [UserController::class, 'updateProfilePicture']);
        Route::delete('/account', [UserController::class, 'deleteAccount']);
        
        Route::post('/refresh', [AuthController::class, 'refresh']); 
        Route::post('/logout', [AuthController::class, 'logout']); 
    });
});