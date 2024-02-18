<?php

namespace App\Transformers;

use App\Models\Post;
use League\Fractal\TransformerAbstract;

class PostSimpleTransformer extends TransformerAbstract
{
    public function transform(Post $post): array
    {
        return [
            'id' => $post->id,
            'caption' => $post->caption,
            'price' => $post->price,
            'media_type' => $post->media_type,
            'media' => [
                'original_url' => $post->getFirstMedia()->original_url,
                'extension' => $post->getFirstMedia()->extension,
                'size' => $post->getFirstMedia()->size,
            ],

        ];
    }
}
