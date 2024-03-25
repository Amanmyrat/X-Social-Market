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
        if ($this->resource->notifiable) {
            $className = (new ReflectionClass($this->resource->notifiable))->getShortName();
            $notificationType = strtolower($className);

            // Remove 'post' prefix if present
            $prefix = 'post'; // Define the prefix you want to remove
            if (str_starts_with($notificationType, $prefix)) {
                $notificationType = substr($notificationType, strlen($prefix));
            }
        } else {
            $notificationType = 'default';
        }

        return [
            'notification_type' => $notificationType,
            'post' => [
                'id' => $this->resource->post->id,
                'media_type' => $this->resource->post->media_type,
                'media' => $this->resource->post->first_image_urls,
            ],
            'user' => [
                'id' => $this->resource->notifiable->user->id,
                'username' => $this->resource->notifiable->user->username,
                'full_name' => $this->resource->notifiable->user->profile?->full_name,
                'image' => $this->resource->notifiable->user->profile?->image_urls,
            ],
            'created_at' => $this->resource->created_at,
        ];
    }
}
