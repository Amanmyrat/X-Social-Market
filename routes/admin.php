<?php

use App\Http\Controllers\Admin\AdminAdminController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBrandController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminColorController;
use App\Http\Controllers\Admin\AdminCommentController;
use App\Http\Controllers\Admin\AdminExistenceController;
use App\Http\Controllers\Admin\AdminLocationController;
use App\Http\Controllers\Admin\AdminPostController;
use App\Http\Controllers\Admin\AdminPostReportController;
use App\Http\Controllers\Admin\AdminReportTypeController;
use App\Http\Controllers\Admin\AdminSizeController;
use App\Http\Controllers\Admin\AdminStoryController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminUserReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin api Routes
|--------------------------------------------------------------------------
|
*/

Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::group(['prefix' => 'admin', 'middleware' => ['role:super-admin|admin']], function () {
    Route::middleware(['auth:sanctum', 'type.admin'])->group(function () {
        Route::prefix('categories')->middleware('permission:manage-categories')
            ->group(function () {
                Route::post('/', [AdminCategoryController::class, 'list']);
                Route::post('/create', [AdminCategoryController::class, 'create']);
                Route::get('/{category}', [AdminCategoryController::class, 'categoryDetails']);
                Route::post('/update/{category}', [AdminCategoryController::class, 'update']);
                Route::post('/delete', [AdminCategoryController::class, 'delete']);
            });

        Route::prefix('brands')->middleware('permission:manage-brands')
            ->group(function () {
                Route::post('/', [AdminBrandController::class, 'list']);
                Route::post('/create', [AdminBrandController::class, 'create']);
                Route::get('/{brand}', [AdminBrandController::class, 'brandDetails']);
                Route::post('/update/{brand}', [AdminBrandController::class, 'update']);
                Route::post('/delete', [AdminBrandController::class, 'delete']);
            });

        Route::middleware(['permission:manage-options'])->group(function () {
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
        });

        Route::middleware(['permission:manage-reports'])->group(function () {
            Route::prefix('report/types')->group(function () {
                Route::post('/', [AdminReportTypeController::class, 'list']);
                Route::post('/create', [AdminReportTypeController::class, 'create']);
                Route::get('/{reportType}', [AdminReportTypeController::class, 'reportTypeDetails']);
                Route::post('/update/{reportType}', [AdminReportTypeController::class, 'update']);
                Route::post('/delete', [AdminReportTypeController::class, 'delete']);
            });

            Route::prefix('post/reports')->group(function () {
                Route::post('/', [AdminPostReportController::class, 'list']);
                Route::post('/{post}/users', [AdminPostReportController::class, 'reportUsers']);
            });

            Route::prefix('user/reports')->group(function () {
                Route::post('/', [AdminUserReportController::class, 'list']);
                Route::post('/{user}/users', [AdminUserReportController::class, 'reportUsers']);
            });
        });

        Route::prefix('users')->middleware('permission:manage-users')
            ->group(function () {
                Route::post('/', [AdminUserController::class, 'list']);
                Route::get('/{user}', [AdminUserController::class, 'userDetails']);
                Route::post('/update/{user}', [AdminUserController::class, 'update']);
                Route::post('/delete', [AdminUserController::class, 'delete']);
                Route::post('/block/{user}', [AdminUserController::class, 'blockUser']);
                Route::post('/unblock/{user}', [AdminUserController::class, 'unBlockUser']);
            });

        Route::prefix('posts')->middleware('permission:manage-posts')
            ->group(function () {
                Route::post('/', [AdminPostController::class, 'list']);
                Route::post('/delete', [AdminPostController::class, 'delete']);

                Route::middleware(['permission:manage-inactive'])->group(function () {
                    Route::get('/{post}', [AdminPostController::class, 'postDetails']);
                    Route::post('/update/{post}', [AdminPostController::class, 'update']);
                    Route::post('/inactive', [AdminPostController::class, 'inactiveList']);
                });
            });

        Route::prefix('admins')->middleware('role:super-admin')
            ->group(function () {
                Route::post('/', [AdminAdminController::class, 'list']);
                Route::post('/create', [AdminAdminController::class, 'create']);
                Route::get('/{admin}', [AdminAdminController::class, 'adminDetails']);
                Route::post('/update/{admin}', [AdminAdminController::class, 'update']);
                Route::post('/delete', [AdminAdminController::class, 'delete']);
                Route::post('/roles', [AdminAdminController::class, 'roles']);
                Route::post('/permissions', [AdminAdminController::class, 'permissions']);
            });

        Route::prefix('comments')->middleware('permission:manage-inactive')
            ->group(function () {
                Route::post('/', [AdminCommentController::class, 'list']);
                Route::post('/accept/{comment}', [AdminCommentController::class, 'accept']);
                Route::post('/decline/{comment}', [AdminCommentController::class, 'decline']);
            });

        Route::prefix('stories')->middleware('permission:manage-inactive')
            ->group(function () {
                Route::post('/', [AdminStoryController::class, 'list']);
                Route::post('/accept/{story}', [AdminStoryController::class, 'accept']);
                Route::post('/decline/{story}', [AdminStoryController::class, 'decline']);
            });

        Route::post('/check/existence', [AdminExistenceController::class, 'checkExistence']);

    });
});
