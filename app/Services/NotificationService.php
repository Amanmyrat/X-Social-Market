<?php

namespace App\Services;

use App\Enum\NotificationType;
use App\Jobs\ProcessNotification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a post notification.
     *
     * @param User $recipient
     * @param int $postId
     * @param ?int $initiatorId
     * @param NotificationType $notificationType
     * @param ?string $reason
     */
    public static function createPostNotification(User $recipient, ?int $initiatorId, int $postId, NotificationType $notificationType, ?string $reason): void
    {
        $notification = $recipient->notifications()->create(['post_id' => $postId,'initiator_id' => $initiatorId, 'type' => $notificationType, 'reason' => $reason]);
        ProcessNotification::dispatch($notification);
    }

    /**
     * Create a story notification.
     *
     * @param User $recipient
     * @param int $storyId
     * @param NotificationType $notificationType
     * @param ?string $reason
     */
    public static function createStoryNotification(User $recipient, int $storyId, NotificationType $notificationType, ?string $reason): void
    {
        $notification = $recipient->notifications()->create(['story_id' => $storyId, 'type' => $notificationType, 'reason' => $reason]);
        ProcessNotification::dispatch($notification);
    }

}
