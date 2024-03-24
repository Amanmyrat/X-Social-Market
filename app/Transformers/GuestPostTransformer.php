<?php

namespace App\Transformers;

use App\Models\Post;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class GuestPostTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'user',
    ];

    public function transform(Post $post): array
    {
        return [
            'id' => $post->id,
            'caption' => $post->caption,
            'price' => $post->price,
            'description' => $post->description,
            'location' => $post->location,
            'media_type' => $post->media_type,
            'can_comment' => $post->can_comment,
            'created_at' => $post->created_at,
            'rating' => $post->ratings_avg_rating,
            'media' => $post->image_urls,
            'isFavorite' => false,
            'isBookmark' => false,
            'isViewed' => false,
            'is_following' => null,
            'has_unviewed_story' => null,
        ];
    }

    public function includeUser(Post $post): Item
    {
        return $this->item($post->user, new UserSimpleTransformer());
    }
}
