<?php

namespace App\Services;

use App\Contracts\NotifiableModel;
use App\Jobs\ProcessPostNotification;

class NotificationService
{
    /**
     * Create a post notification.
     *
     * @param  NotifiableModel  $notifiable  The instance that triggered the notification.
     * @param  int  $postId  The ID of the related post.
     */
    public static function createPostNotification(NotifiableModel $notifiable, int $postId): void
    {
        $notification = $notifiable->notifications()->create(['post_id' => $postId]);
        ProcessPostNotification::dispatch($notification);
    }
}
