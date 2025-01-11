<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFirebaseMessageNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Message $message;
    public string $deviceToken;

    /**
     * Create a new job instance.
     *
     * @param Message $message
     * @param string $deviceToken
     */
    public function __construct(Message $message, string $deviceToken)
    {
        $this->message = $message;
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
            $firebaseService->sendFirebaseMessageNotification($this->message, $this->deviceToken);
        }
    }
}
