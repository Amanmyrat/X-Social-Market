<?php

namespace App\Http\Resources\Admin\PostComment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'comment' => $this->resource->comment,
            'is_active' => $this->resource->is_active,
            'created_at' => $this->resource->created_at,
            'user' => [
                'id' => $this->resource->user->id,
                'username' => $this->resource->user->username,
            ],
            'post' => [
                'id' => $this->resource->post->id,
                'media_type' => $this->resource->post->media_type,
                'media' => $this->resource->post->first_image_urls,
            ]
        ];
    }
}
