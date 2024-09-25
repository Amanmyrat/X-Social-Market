<?php

namespace App\Transformers;

use App\Models\PostTag;
use League\Fractal\TransformerAbstract;

class PostTagTransformer extends TransformerAbstract
{
    public function transform(PostTag $postTag): array
    {
        return [
            'id' => $postTag->id,
            'tagged_post' => [
                'id' => $postTag->post->id,
                'caption' => $postTag->post->caption,
            ],
            'dx' => $postTag->dx,
            'dy' => $postTag->dy,
            'text_options' => $postTag->text_options,
        ];
    }
}
