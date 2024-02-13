<?php

namespace App\Transformers;

use App\Models\Post;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'user',
    ];

    public function transform(Post $post): array
    {
        $medias = [];
        foreach ($post->getMedia() as $media) {
            array_push($medias, [
                'original_url' => $media->original_url,
                'extension' => $media->extension,
                'size' => $media->size,
            ]);
        }

        return [
            'id' => $post->id,
            'category' => $post->category,
            'caption' => $post->caption,
            'price' => $post->price,
            'description' => $post->description,
            'location' => $post->location,
            'media_type' => $post->media_type,
            'can_comment' => $post->can_comment,
            'rating' => $post->rating(),
            'media' => $medias,
            'isFavorite' => $post->getIsFavorite(),
            'isBookmark' => $post->getIsBookmark(),
            'isViewed' => $post->getIsViewed(),
            'favorites_count' => $post->favorites_count,
            'comments_count' => $post->comments_count,
            'views_count' => $post->views_count,
            'created_at' => $post->created_at,
            'is_following' => (bool)$post->is_following ?? false,
        ];
    }

    public function includeUser(Post $post): Item
    {
        return $this->item($post->user, new UserSimpleTransformer());
    }
}
