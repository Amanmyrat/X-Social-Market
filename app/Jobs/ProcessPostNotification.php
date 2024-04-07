<?php

namespace App\Jobs;

use App\Events\PostNotificationSent;
use App\Models\PostNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPostNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected PostNotification $notification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PostNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Broadcast the message
        broadcast(new PostNotificationSent($this->notification))->toOthers();
    }
}
