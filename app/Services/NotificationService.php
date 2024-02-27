<?php

namespace App\Services;

use App\Contracts\NotifiableModel;
use App\Models\PostNotification;

class NotificationService
{
    /**
     * Create a post notification.
     *
     * @param  NotifiableModel  $notifiable  The instance that triggered the notification.
     * @param  int  $postId  The ID of the related post.
     */
    public static function createPostNotification(NotifiableModel $notifiable, int $postId): PostNotification
    {
        return $notifiable->notifications()->create(['post_id' => $postId]);
    }
}
