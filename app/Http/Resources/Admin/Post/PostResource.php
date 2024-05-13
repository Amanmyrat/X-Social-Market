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
            return [
                'id' => $this->resource->id,
                'caption' => $this->resource->caption,
                'price' => $this->resource->price,
                'description' => $this->resource->description,
                'location' => $this->resource->location,
                'is_active' => $this->resource->is_active,
                'can_comment' => $this->resource->can_comment,
                'created_at' => $this->resource->created_at,
                'rating' => $this->resource->ratings_avg_rating,
                'favorites_count' => $this->resource->favorites_count,
                'comments_count' => $this->resource->comments_count,
                'views_count' => $this->resource->views_count,
                'bookmarks_count' => $this->resource->bookmarks_count,
                'user' => $this->resource->user->username,
                'category' => $this->resource->category->title,
                'product' => new ProductResource($this->resource->product),
                'media' => $this->resource->image_urls,
            ];
        } else {
            return [
                'id' => $this->resource->id,
                'caption' => $this->resource->caption,
                'user' => $this->resource->user->username,
                'price' => $this->resource->price,
                'is_active' => $this->resource->is_active,
                'category' => $this->resource->category->title,
                'media' => $this->resource->first_image_urls,
            ];
        }
    }
}
