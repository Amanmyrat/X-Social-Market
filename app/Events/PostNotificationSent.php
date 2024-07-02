<?php

namespace App\Events;

use App\Models\PostNotification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use ReflectionClass;

class PostNotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PostNotification $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(PostNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('App.Models.User.' . $this->notification->notifiable->user->id);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'PostNotificationSent';
    }

    public function broadcastWith(): array
    {
        $className = (new ReflectionClass($this->notification->notifiable))->getShortName();
        $notificationType = strtolower($className);

        $prefix = 'post';
        if ($notificationType == $prefix) {
            $notificationType = 'post_blocked';
        } else if (str_starts_with($notificationType, $prefix)) {
            $notificationType = substr($notificationType, strlen($prefix));
        }

        if ($notificationType == 'comment' && $this->notification->comment_id != null) {
            $notificationType = 'comment_added';
        }

        $result = [
            'notification_type' => $notificationType,
            'user' => [
                'id' => $this->notification->notifiable->user->id,
                'username' => $this->notification->notifiable->user->username,
                'full_name' => $this->notification->notifiable->user->profile?->full_name,
                'image' => $this->notification->notifiable->user->profile?->image_urls,
            ],
            'reason' => $this->notification->reason,
            'created_at' => $this->notification->created_at,
        ];

        if ($this->notification->post_id != null) {
            $result += [
                'post' => [
                    'id' => $this->notification->post->id,
                    'media' => $this->notification->post->first_image_urls,
                ]
            ];
        } else if ($this->notification->comment_id != null) {
            $result += [
                'comment' => [
                    'id' => $this->resource->comment->id ?? null,
                    'content' => $this->resource->comment->comment ?? null,
                ],
            ];
        }

        return ['data' => $result];
    }
}
