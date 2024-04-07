<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlockedUserController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\Api\FollowerRequestController;
use App\Http\Controllers\Api\GuestPostController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OptionsController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\PostBookmarkController;
use App\Http\Controllers\Api\PostCommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PostFavoritesController;
use App\Http\Controllers\Api\PostRatingController;
use App\Http\Controllers\Api\PostReportController;
use App\Http\Controllers\Api\PostViewController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\StoryReportController;
use App\Http\Controllers\Api\StoryViewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\UserReportController;
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

Route::prefix('guest/posts')->group(function () {
    Route::post('/recommended', [GuestPostController::class, 'allPosts']);
    Route::post('/{post}/details', [GuestPostController::class, 'postDetails']);
    Route::post('/{post}/comments', [GuestPostController::class, 'comments']);
    Route::post('/{post}/related', [GuestPostController::class, 'relatedPosts']);
    Route::post('/discovery', [GuestPostController::class, 'discoveryPosts']);
    Route::post('/category/{category}', [GuestPostController::class, 'categoryPosts']);
    Route::post('/search', [GuestPostController::class, 'search']);
    Route::post('/filter', [GuestPostController::class, 'filter']);
});

Route::prefix('users')->group(function () {
    Route::post('/otp/send', [OtpController::class, 'sendOTP']);
    Route::post('/otp/confirm', [OtpController::class, 'confirmOTP']);

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/password/reset', [UserController::class, 'resetPassword']);
    Route::post('/check/availability', [UserController::class, 'checkAvailability']);
    Route::post('/list', [UserController::class, 'getAll']);
});

Route::middleware(['auth:sanctum', 'type.user'])->group(function () {

    Route::prefix('users')->group(function () {
        Route::post('/password/update', [UserController::class, 'updatePassword']);
        Route::post('/phone/update', [UserController::class, 'updatePhone']);
        Route::post('/delete', [UserController::class, 'delete']);
        Route::post('/make/seller', [UserController::class, 'makeAccountBusiness']);
        Route::post('/block', [BlockedUserController::class, 'block']);
        Route::post('/unblock', [BlockedUserController::class, 'unblock']);
        Route::post('/block/list', [BlockedUserController::class, 'blockedList']);
        Route::post('/search', [UserController::class, 'search']);
        Route::post('/check/contacts', [UserController::class, 'checkContacts']);
        Route::post('/{user}/report', [UserReportController::class, 'reportUser']);
    });

    Route::prefix('users/profile')->group(function () {
        Route::post('/update', [UserProfileController::class, 'update']);
        Route::post('/get/{user}', [UserProfileController::class, 'get']);
    });

    Route::prefix('users/notifications')->group(function () {
        Route::post('/', [NotificationController::class, 'list']);
        Route::get('/count', [NotificationController::class, 'unreadCount']);
    });

    Route::prefix('users/follow')->group(function () {
        Route::post('/', [FollowerController::class, 'follow']);
        Route::post('/unfollow', [FollowerController::class, 'unfollow']);
        Route::post('/request', [FollowerRequestController::class, 'followRequest']);
        Route::post('/request/{user}/accept', [FollowerRequestController::class, 'accept']);
        Route::post('/request/{user}/decline', [FollowerRequestController::class, 'decline']);
        Route::post('/outgoing/requests', [FollowerRequestController::class, 'followerRequests']);
        Route::post('/incoming/requests', [FollowerRequestController::class, 'followingRequests']);
    });

    Route::post('/followers', [FollowerController::class, 'followers']);
    Route::post('/followings', [FollowerController::class, 'followings']);
    Route::post('/users/{user}/followers', [FollowerController::class, 'userFollowers']);
    Route::post('/users/{user}/followings', [FollowerController::class, 'userFollowings']);

    Route::prefix('posts')->group(function () {
        Route::post('/recommended', [PostController::class, 'recommendedPosts']);
        Route::post('/my', [PostController::class, 'myPosts']);
        Route::post('/user/{user}', [PostController::class, 'userPosts']);
        Route::post('/create', [PostController::class, 'create']);
        Route::post('/{post}/update', [PostController::class, 'update']);
        Route::post('/{post}/delete', [PostController::class, 'delete']);
        Route::post('/{post}/details', [PostController::class, 'postDetails']);
        Route::post('/{post}/related', [PostController::class, 'relatedPosts']);
        Route::post('/discovery', [PostController::class, 'discoveryPosts']);
        Route::post('/category/{category}', [PostController::class, 'categoryPosts']);
        Route::post('/search', [PostController::class, 'search']);
        Route::post('/filter', [PostController::class, 'filter']);

        Route::post('/{post}/comments', [PostCommentController::class, 'comments']);
        Route::post('/{post}/comment', [PostCommentController::class, 'addComment']);

        Route::post('/{post}/ratings', [PostRatingController::class, 'ratings']);
        Route::post('/{post}/rating', [PostRatingController::class, 'addRating']);

        Route::post('/{post}/report', [PostReportController::class, 'reportPost']);
        Route::post('/{post}/views', [PostViewController::class, 'views']);
        Route::post('/{post}/view', [PostViewController::class, 'view']);

        Route::prefix('favorites')->group(function () {
            Route::post('/', [PostFavoritesController::class, 'favorites']);
            Route::post('/{post}/change', [PostFavoritesController::class, 'change']);
            Route::post('/{post}/users', [PostFavoritesController::class, 'favoriteUsers']);
        });

        Route::prefix('bookmarks')->group(function () {
            Route::post('/', [PostBookmarkController::class, 'bookmarks']);
            Route::post('/{post}/change', [PostBookmarkController::class, 'change']);
        });
    });

    Route::prefix('stories')->group(function () {
        Route::post('/recommended', [StoryController::class, 'followingStories']);
        Route::post('/my', [StoryController::class, 'myStories']);
        Route::post('/user/{user}', [StoryController::class, 'userStories']);
        Route::post('/create', [StoryController::class, 'create']);

        Route::post('/{story}/report', [StoryReportController::class, 'reportStory']);
        Route::post('/{story}/views', [StoryViewController::class, 'views']);
        Route::post('/{story}/view', [StoryViewController::class, 'view']);
    });

    Route::prefix('chat')->group(function () {
        Route::post('/create', [ChatController::class, 'createChat']);
        Route::post('/list', [ChatController::class, 'listChats']);
        Route::post('/{chat}/delete', [ChatController::class, 'delete']);

        Route::post('/send/message', [MessageController::class, 'sendMessage']);
        Route::post('/{chat}/messages', [MessageController::class, 'listMessages']);
        Route::post('/{chat}/read', [MessageController::class, 'readAllUnreadMessages']);
        Route::post('/messages/{message}/read', [MessageController::class, 'readMessage']);
        Route::post('/messages/{message}/delete', [MessageController::class, 'delete']);
        Route::post('/messages/{message}/image/{media}/delete', [MessageController::class, 'deleteImage']);
    });

    Route::prefix('options')->group(function () {
        Route::post('/categories', [OptionsController::class, 'categories']);
        Route::post('/locations', [OptionsController::class, 'locations']);
        Route::post('/brands', [OptionsController::class, 'brands']);
        Route::post('/colors', [OptionsController::class, 'colors']);
        Route::post('/sizes', [OptionsController::class, 'sizes']);
        Route::post('/report/types', [OptionsController::class, 'reportTypes']);
    });

});

require __DIR__.'/admin.php';
