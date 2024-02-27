<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlockedUserController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\Api\FollowerRequestController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OptionsController;
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

        Route::post('follow/request', [FollowerRequestController::class, 'followRequest']);
        Route::post('follow/request/{user}/accept', [FollowerRequestController::class, 'accept']);
        Route::post('follow/request/{user}/decline', [FollowerRequestController::class, 'decline']);

        Route::post('{user}/stories', [StoryController::class, 'userStories']);
        Route::post('{user}/posts', [PostController::class, 'userPosts']);

        Route::post('block', [BlockedUserController::class, 'block']);
        Route::post('unblock', [BlockedUserController::class, 'unblock']);

        Route::post('block/list', [BlockedUserController::class, 'blockedList']);

        Route::post('make/seller', [UserController::class, 'makeAccountBusiness']);
        Route::post('search', [UserController::class, 'search']);
        Route::post('/notifications', [NotificationController::class, 'list']);

    });

    Route::prefix('posts')->group(function () {
        Route::post('/', [PostController::class, 'myPosts']);
        Route::post('/create', [PostController::class, 'create']);
        Route::post('/{post}/update', [PostController::class, 'update']);
        Route::post('/{post}/delete', [PostController::class, 'delete']);
        Route::post('/{post}/related', [PostController::class, 'relatedPosts']);

        Route::post('/discovery', [PostController::class, 'discoveryPosts']);
        Route::post('/category/{category}', [PostController::class, 'categoryPosts']);

        Route::post('/search', [PostController::class, 'search']);
        Route::post('/{post}/details', [PostController::class, 'postDetails']);

        Route::post('/favorites/{post}/change', [PostFavoritesController::class, 'change']);
        Route::post('/favorites/{post}/users', [PostFavoritesController::class, 'favoriteUsers']);
        Route::post('/favorites', [PostFavoritesController::class, 'favorites']);

        Route::post('bookmarks', [PostBookmarkController::class, 'bookmarks']);
        Route::post('/bookmarks/{post}/change', [PostBookmarkController::class, 'change']);

        Route::post('/{post}/comments', [PostCommentController::class, 'comments']);
        Route::post('/{post}/comment', [PostCommentController::class, 'addComment']);

        Route::post('/{post}/ratings', [PostRatingController::class, 'ratings']);
        Route::post('/{post}/rating', [PostRatingController::class, 'addRating']);

        Route::post('/{post}/spam', [PostSpamController::class, 'spamPost']);

        Route::post('/{post}/views', [PostViewController::class, 'views']);
        Route::post('/views/{story}/view', [PostViewController::class, 'view']);

        Route::post('/all/list', [PostController::class, 'allPosts']);
    });

    Route::prefix('stories')->group(function () {
        Route::post('/', [StoryController::class, 'myStories']);
        Route::post('/create', [StoryController::class, 'create']);

        Route::post('/{story}/spam', [StorySpamController::class, 'spamStory']);
        Route::post('/{story}/views', [StoryViewController::class, 'views']);
        Route::post('/views/{story}/view', [StoryViewController::class, 'view']);
    });

    Route::post('followers', [FollowerController::class, 'followers']);
    Route::post('users/{user}/followers', [FollowerController::class, 'userFollowers']);
    Route::post('users/{user}/followings', [FollowerController::class, 'userFollowings']);

    Route::prefix('followings')->group(function () {
        Route::post('/', [FollowerController::class, 'followings']);
        Route::post('stories', [StoryController::class, 'followingStories']);
    });

    Route::post('follow/outgoing/requests', [FollowerRequestController::class, 'followerRequests']);
    Route::post('follow/incoming/requests', [FollowerRequestController::class, 'followingRequests']);

    Route::post('/chat/create', [ChatController::class, 'createChat']);
    Route::post('/chat/list', [ChatController::class, 'listChats']);
    Route::post('/chat/{chat}/delete', [ChatController::class, 'delete']);

    Route::post('/chat/send/message', [MessageController::class, 'sendMessage']);
    Route::post('/chat/{chatId}/messages', [MessageController::class, 'listMessages']);
    Route::post('/chat/{chat}/read', [MessageController::class, 'readAllUnreadMessages']);

    Route::post('/message/{message}/read', [MessageController::class, 'readMessage']);
    Route::post('/messages/{message}/delete', [MessageController::class, 'delete']);
    Route::post('/message/{message}/image/{media}/delete', [MessageController::class, 'deleteImage']);

    Route::post('/categories', [OptionsController::class, 'categories']);
    Route::post('/locations', [OptionsController::class, 'locations']);
    Route::post('/brands', [OptionsController::class, 'brands']);
    Route::post('/colors', [OptionsController::class, 'colors']);
    Route::post('/sizes', [OptionsController::class, 'sizes']);

});
Route::post('/users/all/list', [UserController::class, 'getAll']);

Route::prefix('spam')->group(function () {
    Route::post('types/create', [SpamTypeController::class, 'create']);
    Route::post('types', [SpamTypeController::class, 'types']);
});

Route::post('guest/posts/all/list', [PostController::class, 'guestAllPosts']);

require __DIR__.'/admin.php';
