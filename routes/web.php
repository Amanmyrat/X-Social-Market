<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceRedirectController;
use App\Http\Controllers\DeepLinkController;

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

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/device-redirect', [DeviceRedirectController::class, 'redirectToPlatform']);

// Deep linking routes
Route::get('/profile/{profileId}', [DeepLinkController::class, 'profile'])->name('deeplink.profile');
Route::get('/post/{postId}', [DeepLinkController::class, 'post'])->name('deeplink.post');

// Well-known files for deep linking
Route::get('/.well-known/apple-app-site-association', function () {
    $content = file_get_contents(resource_path('well-known/apple-app-site-association'));
    return response($content, 200)
        ->header('Content-Type', 'application/json');
});

Route::get('/.well-known/assetlinks.json', function () {
    $content = file_get_contents(resource_path('well-known/assetlinks.json'));
    return response($content, 200)
        ->header('Content-Type', 'application/json');
});
