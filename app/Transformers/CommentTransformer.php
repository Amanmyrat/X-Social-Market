<?php

namespace App\Transformers;

use App\Models\PostComment;
use Carbon\Carbon;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'children',
    ];

    public function transform(PostComment $comment): array
    {
        return [
            'id' => $comment->id,
            'user' => $comment->user,
            'comment' => $comment->comment,
            'date' => Carbon::parse($comment->created_at)->format('d.m.Y'),
        ];
    }

    public function includeChildren(PostComment $comment): Collection
    {
        return $this->collection($comment->children, new CommentTransformer());
    }
}
