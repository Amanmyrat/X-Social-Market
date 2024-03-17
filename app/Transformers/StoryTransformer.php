<?php

namespace App\Transformers;

use App\Models\Story;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class StoryTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'post', 'user',
    ];

    public function transform(Story $story): array
    {
        return [
            'id' => $story->id,
            'image' => $story->image_urls,
            'isFavorite' => $story->getIsFavorite(),
            'isViewed' => $story->getIsViewed(),
            'created_at' => $story->created_at,
        ];
    }

    public function includePost(Story $story): ?Item
    {
        if ($story->post) {
            return $this->item($story->post, new PostSimpleTransformer());
        } else {
            return null;
        }
    }

    public function includeUser(Story $story): Item
    {
        return $this->item($story->user, new UserSimpleTransformer());
    }
}
