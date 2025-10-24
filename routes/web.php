<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReverbStatusController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Reverb Status Dashboard (like old laravel-websockets)
Route::get('/reverb-status', [ReverbStatusController::class, 'index'])->name('reverb.status');

// API endpoint to check if Reverb is responding (with real-time stats)
Route::get('/api/reverb-check', [ReverbStatusController::class, 'apiCheck'])->name('reverb.check');
