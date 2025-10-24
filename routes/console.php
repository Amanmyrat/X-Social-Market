<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
|
| Here you may define all of your scheduled tasks. These will be
| automatically registered with Laravel's scheduler.
|
*/

Schedule::call(function () {
    User::where('is_online', true)
        ->where('last_activity', '<', now()->subMinutes())
        ->update(['is_online' => false]);
})->everyMinute();

Schedule::command('stories:delete-overdue')->everySixHours();
