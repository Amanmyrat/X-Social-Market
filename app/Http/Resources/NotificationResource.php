<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     *
     */
    public function toArray(Request $request): array
    {
        $result = [
            'notification_type' => $this->resource->type,
            'user' => $this->resource->initiator != null ? [
                'id' => $this->resource->initiator->id,
                'username' => $this->resource->initiator->username,
                'full_name' => $this->resource->initiator->profile?->full_name,
                'image' => $this->resource->initiator->profile?->image_urls,
            ] : null,
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
        } else if ($this->resource->story_id != null) {
            $result += [
                'story' => [
                    'id' => $this->resource->story->id,
                    'content' => $this->resource->story->image_urls,
                ],
            ];
        }

        return $result;
    }
}
