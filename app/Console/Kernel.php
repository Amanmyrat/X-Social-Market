<?php

namespace App\Console;

use App\Console\Commands\RefreshMaterializedView;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            User::where('is_online', true)
                ->where('last_activity', '<', now()->subMinutes())
                ->update(['is_online' => false]);
        })->everyMinute();
        $schedule->command(RefreshMaterializedView::class)->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
