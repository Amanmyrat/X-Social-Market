<?php

namespace App\Transformers;

use App\Models\PostComment;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'children', 'user',
    ];

    public function transform(PostComment $comment): array
    {
        return [
            'id' => $comment->id,
            'comment' => $comment->comment,
            'date' => $comment->created_at,
        ];
    }

    public function includeChildren(PostComment $comment): Collection
    {
        return $this->collection($comment->children()->where('is_active', true)->whereNull('blocked_at')->get(), new CommentTransformer());
    }

    public function includeUser(PostComment $comment): Item
    {
        return $this->item($comment->user, new UserSimpleTransformer());
    }
}
