<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('users')->group(function () {
    Route::post('otp/send', [OtpController::class, 'sendOTP']);
    Route::post('otp/confirm', [OtpController::class, 'confirmOTP']);

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::prefix('users')->group(function () {
        Route::post('update', [UserController::class, 'update']);
        Route::post('password/update', [UserController::class, 'updatePassword']);
        Route::post('password/new', [UserController::class, 'newPassword']);
        Route::post('delete', [UserController::class, 'delete']);

        Route::post('profile/update', [UserProfileController::class, 'update']);

        Route::post('follow', [FollowerController::class, 'follow']);
        Route::post('unfollow', [FollowerController::class, 'unfollow']);
    });

    Route::prefix('posts')->group(function () {
        Route::post('/', [PostController::class, 'myPosts']);
        Route::post('create', [PostController::class, 'create']);

    });

    Route::post('followers', [FollowerController::class, 'followers']);
    Route::post('followings', [FollowerController::class, 'followings']);

    Route::post('stories/create', [StoryController::class, 'create']);
    Route::post('stories', [StoryController::class, 'myStories']);

});

