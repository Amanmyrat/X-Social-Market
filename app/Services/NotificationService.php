<?php

namespace App\Services;

use App\Contracts\NotifiableModel;
use App\Jobs\ProcessPostNotification;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Story;

class NotificationService
{
    /**
     * Create a post notification.
     *
     * @param NotifiableModel $notifiable The instance that triggered the notification.
     * @param int $postId The ID of the related post.
     */
    public static function createPostInteractionNotificationToPostAuthor(NotifiableModel $notifiable, int $postId): void
    {
        $notification = $notifiable->notifications()->create(['post_id' => $postId]);
        ProcessPostNotification::dispatch($notification);
    }

    /**
     * Create a post status notification.
     *
     * @param Post $notifiable The instance that triggered the notification.
     * @param int $postId The ID of the related post.
     * @param string $reason The reason of action.
     */
    public static function createPostStatusNotification(Post $notifiable, int $postId, string $reason): void
    {
        $notification = $notifiable->notifications()->create(['post_id' => $postId, 'reason' => $reason]);
        ProcessPostNotification::dispatch($notification);
    }

    public static function createCommentNotificationToCommentCreator(PostComment $notifiable, int $commentId): void
    {
        $notification = $notifiable->notifications()->create(['comment_id' => $commentId]);
        ProcessPostNotification::dispatch($notification);
    }

    public static function createCommentRejectNotificationToCommentCreator(PostComment $notifiable, int $commentId, string $reason): void
    {
        $notification = $notifiable->notifications()->create(['comment_id' => $commentId, 'reason' => $reason]);
        ProcessPostNotification::dispatch($notification);
    }

    public static function createStoryRejectNotification(Story $story, string $reason): void
    {
        $notification = $story->notifications()->create(['reason' => $reason]);
        ProcessPostNotification::dispatch($notification);
    }
}
