<?php

namespace App\Services;

use App\Contracts\NotifiableModel;
use App\Jobs\ProcessPostNotification;

class NotificationService
{
    /**
     * Create a post notification.
     *
     * @param NotifiableModel $notifiable The instance that triggered the notification.
     * @param int $postId The ID of the related post.
     */
    public static function createPostNotification(NotifiableModel $notifiable, int $postId): void
    {
        $notification = $notifiable->notifications()->create(['post_id' => $postId]);
        ProcessPostNotification::dispatch($notification);
    }

    /**
     * Create a post status notification.
     *
     * @param NotifiableModel $notifiable The instance that triggered the notification.
     * @param int $postId The ID of the related post.
     * @param string $reason The reason of action.
     */
    public static function createPostStatusNotification(NotifiableModel $notifiable, int $postId, string $reason): void
    {
        $notification = $notifiable->notifications()->create(['post_id' => $postId, 'reason' => $reason]);
        ProcessPostNotification::dispatch($notification);
    }

    public static function createCommentNotification(NotifiableModel $notifiable, int $commentId): void
    {
        $notification = $notifiable->notifications()->create(['comment_id' => $commentId]);
        ProcessPostNotification::dispatch($notification);
    }
}
