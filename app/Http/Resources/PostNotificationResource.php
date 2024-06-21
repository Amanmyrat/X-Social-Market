<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use ReflectionClass;
use ReflectionException;

class PostNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     *
     * @throws ReflectionException
     */
    public function toArray($request): array
    {
        $className = (new ReflectionClass($this->resource->notifiable))->getShortName();
        $notificationType = strtolower($className);

        $prefix = 'post';
        if ($notificationType == $prefix) {
            $notificationType = 'post_blocked';
        } else if (str_starts_with($notificationType, $prefix)) {
            $notificationType = substr($notificationType, strlen($prefix));
        }

        if($notificationType == 'comment' && $this->resource->comment_id != null){
            $notificationType = 'comment_added';
        }

        $result = [
            'notification_type' => $notificationType,
            'user' => [
                'id' => $this->resource->notifiable->user->id,
                'username' => $this->resource->notifiable->user->username,
                'full_name' => $this->resource->notifiable->user->profile?->full_name,
                'image' => $this->resource->notifiable->user->profile?->image_urls,
            ],
            'reason' => $this->resource->reason,
            'created_at' => $this->resource->created_at,
        ];

        if ($this->resource->post_id != null) {
            $result += [
                'post' => [
                    'id' => $this->resource->post->id,
                    'media' => $this->resource->post->first_image_urls,
                ]
            ];
        } else if ($this->resource->comment_id != null) {
            $result += [
                'comment' => [
                    'id' => $this->resource->comment->id ?? null,
                    'content' => $this->resource->comment->comment ?? null,
                ],
            ];
        }

        return $result;
    }
}
