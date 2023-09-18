<?php

use App\Http\Controllers\Api\AuthController;
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

Route::post('user/register/otp/send', [AuthController::class, 'sendRegisterOTP']);
Route::post('user/register/otp/confirm', [AuthController::class, 'confirmRegisterOTP']);
Route::post('user/register', [AuthController::class, 'register']);
Route::post('user/login', [AuthController::class, 'login']);


Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('user/update', [AuthController::class, 'update']);
    Route::post('user/password/update', [AuthController::class, 'updatePassword']);
    Route::post('user/delete', [AuthController::class, 'delete']);

});

