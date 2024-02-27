<?php

namespace App\Services;

use App\Models\PostNotification;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Create a post notification.
     *
     * @param  Model  $notifiable  The instance that triggered the notification.
     * @param  int  $postId  The ID of the related post.
     */
    public static function createPostNotification(Model $notifiable, int $postId): PostNotification
    {
        return $notifiable->notifications()->create(['post_id' => $postId]);
    }
}
