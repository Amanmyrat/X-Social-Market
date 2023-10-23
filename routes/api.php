<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlockedUserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PostBookmarkController;
use App\Http\Controllers\Api\PostCommentController;
use App\Http\Controllers\Api\PostRatingController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostFavoritesController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\StoryViewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserProfileController;
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

        Route::post('{user_id}/stories', [StoryController::class, 'userStories']);
        Route::post('{user_id}/posts', [PostController::class, 'userPosts']);

        Route::post('block', [BlockedUserController::class, 'block']);
        Route::post('unblock', [BlockedUserController::class, 'unblock']);

        Route::post('block/list', [BlockedUserController::class, 'blockedList']);
    });

    Route::prefix('posts')->group(function () {
        Route::post('/', [PostController::class, 'myPosts']);
        Route::post('create', [PostController::class, 'create']);

        Route::post('favorites', [PostFavoritesController::class, 'favorites']);
        Route::post('/favorites/{post}/change', [PostFavoritesController::class, 'change']);

        Route::post('bookmarks', [PostBookmarkController::class, 'bookmarks']);
        Route::post('/bookmarks/{post}/change', [PostBookmarkController::class, 'change']);

        Route::post('/{post}/comments', [PostCommentController::class, 'comments']);
        Route::post('/{post}/comment', [PostCommentController::class, 'addComment']);

        Route::post('/{post}/ratings', [PostRatingController::class, 'ratings']);
        Route::post('/{post}/rating', [PostRatingController::class, 'addRating']);

        Route::post('/search', [PostController::class, 'search']);
        Route::post('/{post}', [PostController::class, 'postDetails']);
    });

    Route::prefix('stories')->group(function () {
        Route::post('/', [StoryController::class, 'myStories']);
        Route::post('/create', [StoryController::class, 'create']);

        Route::post('/{story}/views', [StoryViewController::class, 'views']);
        Route::post('/views/{story}/view', [StoryViewController::class, 'view']);
    });

    Route::post('followers', [FollowerController::class, 'followers']);

    Route::prefix('followings')->group(function () {
        Route::post('/', [FollowerController::class, 'followings']);
        Route::post('stories', [StoryController::class, 'followingStories']);
        Route::post('posts', [PostController::class, 'followingPosts']);
    });

});

Route::prefix('post/categories')->group(function () {
    Route::post('create', [CategoryController::class, 'create']);
    Route::post('/', [CategoryController::class, 'categories']);
});

