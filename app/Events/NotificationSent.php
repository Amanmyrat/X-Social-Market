<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Notification $notification;

    /**
     * Create a new event instance.
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('App.Models.User.' . $this->notification->recipient_id);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'NotificationSent';
    }

    public function broadcastWith(): array
    {
        $result = [
            'notification_type' => $this->notification->type,
            'user' => $this->notification->initiator != null ? [
                'id' => $this->notification->initiator->id,
                'username' => $this->notification->initiator->username,
                'full_name' => $this->notification->initiator->profile?->full_name,
                'image' => $this->notification->initiator->profile?->image_urls,
            ] : null,
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
        } else if ($this->notification->story_id != null) {
            $result += [
                'story' => [
                    'id' => $this->notification->story->id,
                    'content' => $this->notification->story->image_urls,
                ],
            ];
        }

        return ['data' => $result];
    }
}
