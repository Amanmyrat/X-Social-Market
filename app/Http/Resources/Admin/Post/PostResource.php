<?php

namespace App\Http\Resources\Admin\Post;

use App\Http\Resources\Admin\Product\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    private bool $detailsEnabled;

    public function __construct($resource, bool $detailsEnabled = false)
    {
        parent::__construct($resource);
        $this->detailsEnabled = $detailsEnabled;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        if ($this->detailsEnabled) {

            $medias = [];
            foreach ($this->resource->getMedia() as $media) {
                array_push($medias, [
                    'original_url' => $media->original_url,
                    'extension' => $media->extension,
                    'size' => $media->size,
                ]);
            }

            return [
                'id' => $this->resource->id,
                'caption' => $this->resource->caption,
                'price' => $this->resource->price,
                'description' => $this->resource->description,
                'location' => $this->resource->location,
                'media_type' => $this->resource->media_type,
                'can_comment' => $this->resource->can_comment,
                'created_at' => $this->resource->created_at,
                'rating' => $this->resource->ratings_avg_rating,
                'favorites_count' => $this->resource->favorites_count,
                'comments_count' => $this->resource->comments_count,
                'views_count' => $this->resource->views_count,
                'user' => $this->resource->user->username,
                'category' => $this->resource->category->title,
                'product' => new ProductResource($this->resource->product),
                'media' => $medias,
            ];
        } else {
            return [
                'id' => $this->resource->id,
                'caption' => $this->resource->caption,
                'user' => $this->resource->user->username,
                'price' => $this->resource->price,
                'category' => $this->resource->category->title,
                'media_type' => $this->resource->media_type,
                'media' => [
                    'original_url' => $this->resource->getFirstMedia()->original_url,
                    'extension' => $this->resource->getFirstMedia()->extension,
                    'size' => $this->resource->getFirstMedia()->size,
                ],
            ];
        }
    }
}
