<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBrandController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminColorController;
use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\Admin\AdminSizeController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlockedUserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PostBookmarkController;
use App\Http\Controllers\Api\PostCommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostFavoritesController;
use App\Http\Controllers\Api\PostRatingController;
use App\Http\Controllers\Api\PostSpamController;
use App\Http\Controllers\Api\PostViewController;
use App\Http\Controllers\Api\SpamTypeController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\StorySpamController;
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
//Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::prefix('users')->group(function () {
    Route::post('otp/send', [OtpController::class, 'sendOTP']);
    Route::post('otp/confirm', [OtpController::class, 'confirmOTP']);

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'type.user'])->group(function () {
    Route::prefix('users')->group(function () {
        Route::post('update', [UserController::class, 'update']);
        Route::post('password/update', [UserController::class, 'updatePassword']);
        Route::post('password/new', [UserController::class, 'newPassword']);
        Route::post('delete', [UserController::class, 'delete']);
        Route::post('phone/update', [UserController::class, 'updatePhone']);

        Route::post('profile/update', [UserProfileController::class, 'update']);
        Route::post('profile/get/{user}', [UserProfileController::class, 'get']);

        Route::post('follow', [FollowerController::class, 'follow']);
        Route::post('unfollow', [FollowerController::class, 'unfollow']);

        Route::post('{user_id}/stories', [StoryController::class, 'userStories']);
        Route::post('{user_id}/posts', [PostController::class, 'userPosts']);

        Route::post('block', [BlockedUserController::class, 'block']);
        Route::post('unblock', [BlockedUserController::class, 'unblock']);

        Route::post('block/list', [BlockedUserController::class, 'blockedList']);

        Route::post('make/seller', [UserController::class, 'makeAccountBusiness']);
    });

    Route::prefix('posts')->group(function () {
        Route::post('/', [PostController::class, 'myPosts']);
        Route::post('create', [PostController::class, 'create']);
        Route::post('/{post}/delete', [PostController::class, 'delete']);

        Route::post('favorites', [PostFavoritesController::class, 'favorites']);
        Route::post('/favorites/{post}/change', [PostFavoritesController::class, 'change']);
        Route::post('favorites/{post}/users', [PostFavoritesController::class, 'favoriteUsers']);

        Route::post('bookmarks', [PostBookmarkController::class, 'bookmarks']);
        Route::post('/bookmarks/{post}/change', [PostBookmarkController::class, 'change']);

        Route::post('/{post}/comments', [PostCommentController::class, 'comments']);
        Route::post('/{post}/comment', [PostCommentController::class, 'addComment']);

        Route::post('/{post}/ratings', [PostRatingController::class, 'ratings']);
        Route::post('/{post}/rating', [PostRatingController::class, 'addRating']);

        Route::post('/search', [PostController::class, 'search']);
        Route::post('/{post}', [PostController::class, 'postDetails']);

        Route::post('/{post}/spam', [PostSpamController::class, 'spamPost']);

        Route::post('/{post}/views', [PostViewController::class, 'views']);
        Route::post('/views/{story}/view', [PostViewController::class, 'view']);
    });

    Route::prefix('stories')->group(function () {
        Route::post('/', [StoryController::class, 'myStories']);
        Route::post('/create', [StoryController::class, 'create']);

        Route::post('/{story}/spam', [StorySpamController::class, 'spamStory']);
        Route::post('/{story}/views', [StoryViewController::class, 'views']);
        Route::post('/views/{story}/view', [StoryViewController::class, 'view']);
    });

    Route::post('followers', [FollowerController::class, 'followers']);

    Route::prefix('followings')->group(function () {
        Route::post('/', [FollowerController::class, 'followings']);
        Route::post('stories', [StoryController::class, 'followingStories']);
        Route::post('posts', [PostController::class, 'followingPosts']);
    });

    Route::post('/chat/create', [ChatController::class, 'createChat']);
    Route::post('/chat/list', [ChatController::class, 'listChats']);

    Route::post('/chat/send/message', [MessageController::class, 'sendMessage']);
    Route::post('/chat/{chatId}/messages', [MessageController::class, 'listMessages']);
    Route::post('/message/{messageId}/read', [MessageController::class, 'readMessage']);
    Route::post('/chat/{chatId}/read', [MessageController::class, 'readAllUnreadMessages']);

});
Route::post('/categories', [CategoryController::class, 'categories']);
Route::post('/locations', [LocationController::class, 'list']);

Route::prefix('spam')->group(function () {
    Route::post('types/create', [SpamTypeController::class, 'create']);
    Route::post('types', [SpamTypeController::class, 'types']);
});

Route::post('posts/all/list', [PostController::class, 'allPosts']);
Route::post('users/all/list', [UserController::class, 'getAll']);

Route::prefix('admin')->group(function () {

    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'type.admin'])->group(function () {
        Route::prefix('categories')->group(function () {
            Route::post('/', [AdminCategoryController::class, 'list']);
            Route::post('/create', [AdminCategoryController::class, 'create']);
            Route::get('/{category}', [AdminCategoryController::class, 'categoryDetails']);
            Route::post('/update/{category}', [AdminCategoryController::class, 'update']);
            Route::post('/delete', [AdminCategoryController::class, 'delete']);
        });

        Route::prefix('brands')->group(function () {
            Route::post('/', [AdminBrandController::class, 'list']);
            Route::post('/create', [AdminBrandController::class, 'create']);
            Route::get('/{brand}', [AdminBrandController::class, 'brandDetails']);
            Route::post('/update/{brand}', [AdminBrandController::class, 'update']);
            Route::post('/delete', [AdminBrandController::class, 'delete']);
        });

        Route::prefix('locations')->group(function () {
            Route::post('/', [AdminLocationController::class, 'list']);
            Route::post('/create', [AdminLocationController::class, 'create']);
            Route::get('/{location}', [AdminLocationController::class, 'locationDetails']);
            Route::post('/update/{location}', [AdminLocationController::class, 'update']);
            Route::post('/delete', [AdminLocationController::class, 'delete']);
        });

        Route::prefix('users')->group(function () {
            Route::post('/', [AdminUserController::class, 'list']);
            Route::get('/{user}', [AdminUserController::class, 'userDetails']);
            Route::post('/update/{user}', [AdminUserController::class, 'update']);
            Route::post('/delete', [AdminUserController::class, 'delete']);
            Route::post('/block/{user}', [AdminUserController::class, 'blockUser']);
            Route::post('/unblock/{user}', [AdminUserController::class, 'unBlockUser']);
        });

        Route::prefix('colors')->group(function () {
            Route::post('/', [AdminColorController::class, 'list']);
            Route::post('/create', [AdminColorController::class, 'create']);
            Route::get('/{color}', [AdminColorController::class, 'colorDetails']);
            Route::post('/update/{color}', [AdminColorController::class, 'update']);
            Route::post('/delete', [AdminColorController::class, 'delete']);
        });

        Route::prefix('sizes')->group(function () {
            Route::post('/', [AdminSizeController::class, 'list']);
            Route::post('/create', [AdminSizeController::class, 'create']);
            Route::get('/{size}', [AdminSizeController::class, 'sizeDetails']);
            Route::post('/update/{size}', [AdminSizeController::class, 'update']);
            Route::post('/delete', [AdminSizeController::class, 'delete']);
        });

    });
});
