<?php

namespace App\Transformers;

use App\Models\Post;
use App\Models\Story;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'user'
    ];

    public function transform(Post $post): array
    {
        $medias = array();
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
        ];
    }

    public function includeUser(Post $post): Item
    {
        return $this->item($post->user, new UserSimpleTransformer());
    }
}
