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
            'description' => $post->description,
            'media_type' => $post->media_type,
            'media' => [
                'original_url' => $post->getMedia()[0]->original_url,
                'extension' => $post->getMedia()[0]->extension,
                'size' => $post->getMedia()[0]->size,
            ],

        ];
    }
}
