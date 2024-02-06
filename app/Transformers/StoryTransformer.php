<?php

namespace App\Transformers;

use App\Models\Story;
use App\Models\User;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\TransformerAbstract;

class StoryTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'post', 'user'
    ];

    public function transform(Story $story): array
    {
        return [
            'id' => $story->id,
            'image' => isset($story->image) ? url('uploads/stories/'.$story->image) : null,
            'isFavorite' => $story->getIsFavorite(),
            'isViewed' => $story->getIsViewed(),
            'created_at' => $story->created_at
        ];
    }

    public function includePost(Story $story): ?Item
    {
        if($story->post){
            return $this->item($story->post, new PostTransformer());
        }
        else{
            return null;
        }
    }

    public function includeUser(Story $story)
    {
        return $this->item($story->user, new UserSimpleTransformer());
    }
}
