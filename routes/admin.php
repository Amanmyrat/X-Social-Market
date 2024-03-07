<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBrandController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminColorController;
use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminPostReportController;
use App\Http\Controllers\Admin\AdminReportTypeController;
use App\Http\Controllers\Admin\AdminSizeController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin api Routes
|--------------------------------------------------------------------------
|
*/

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

        Route::prefix('report/types')->group(function () {
            Route::post('/', [AdminReportTypeController::class, 'list']);
            Route::post('/create', [AdminReportTypeController::class, 'create']);
            Route::get('/{reportType}', [AdminReportTypeController::class, 'reportTypeDetails']);
            Route::post('/update/{reportType}', [AdminReportTypeController::class, 'update']);
            Route::post('/delete', [AdminReportTypeController::class, 'delete']);
        });

        Route::prefix('users')->group(function () {
            Route::post('/', [AdminUserController::class, 'list']);
            Route::get('/{user}', [AdminUserController::class, 'userDetails']);
            Route::post('/update/{user}', [AdminUserController::class, 'update']);
            Route::post('/delete', [AdminUserController::class, 'delete']);
            Route::post('/block/{user}', [AdminUserController::class, 'blockUser']);
            Route::post('/unblock/{user}', [AdminUserController::class, 'unBlockUser']);
        });

        Route::prefix('posts')->group(function () {
            Route::post('/', [AdminPostController::class, 'list']);
            Route::get('/{post}', [AdminPostController::class, 'postDetails']);
            Route::post('/delete', [AdminPostController::class, 'delete']);
        });

        Route::prefix('post/reports')->group(function () {
            Route::post('/', [AdminPostReportController::class, 'list']);
            Route::post('/{post}/users', [AdminPostReportController::class, 'reportUsers']);
        });
    });
});
