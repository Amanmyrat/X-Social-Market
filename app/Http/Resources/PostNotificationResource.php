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
                'media' => [
                    'original_url' => $this->resource->post->getFirstMedia()->original_url,
                    'extension' => $this->resource->post->getFirstMedia()->extension,
                    'size' => $this->resource->post->getFirstMedia()->size,
                ],
            ],
            'user' => [
                'id' => $this->resource->post->user->id,
                'username' => $this->resource->post->user->username,
                'full_name' => $this->resource->post->user->profile?->full_name,
                'profile_image' => $this->resource->post->user->profile?->profile_image ? url('uploads/user/profile/'.$this->resource->post->user->profile?->profile_image) : null,
            ],
            'created_at' => $this->resource->created_at,
        ];
    }
}
