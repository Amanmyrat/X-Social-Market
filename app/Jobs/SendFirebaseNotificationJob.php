<?php

namespace App\Jobs;

use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFirebaseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $notificationId;
    public string $deviceToken;

    /**
     * Create a new job instance.
     *
     * @param int $notificationId
     * @param string $deviceToken
     */
    public function __construct(int $notificationId, string $deviceToken)
    {
        $this->notificationId = $notificationId;
        $this->deviceToken = $deviceToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $firebaseService = new FirebaseNotificationService();

        if ($this->deviceToken) {
            $firebaseService->sendFirebaseNotification($this->notificationId, $this->deviceToken);
        }
    }
}
