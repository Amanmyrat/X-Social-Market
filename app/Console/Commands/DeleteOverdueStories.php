<?php

namespace App\Console\Commands;

use App\Models\Story;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class DeleteOverdueStories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stories:delete-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete stories that are overdue based on the valid_until field';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $overdueStories = Story::where('valid_until', '<', $now)->get();

        $deletedCount = 0;

        foreach ($overdueStories as $story) {
            $story->delete();
            $deletedCount++;
        }

        // Inform how many stories were deleted
        if ($deletedCount > 0) {
            $this->info("{$deletedCount} overdue stories deleted successfully.");
        } else {
            $this->info("No overdue stories found to delete.");
        }
    }
}
