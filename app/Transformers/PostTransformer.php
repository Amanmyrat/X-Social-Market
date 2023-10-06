<?php

namespace App\Transformers;

use App\Models\Post;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    #[ArrayShape(['id' => "mixed", 'caption' => "mixed", 'location' => "mixed", 'media_type' => "mixed", 'can_comment' => "mixed", 'media' => "\Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection"])]
    public function transform(Post $post): array
    {
        $medias = array();
        foreach ($post->getMedia() as $media){
            array_push($medias, [
                'original_url' => $media->original_url,
                'extension' => $media->extension,
                'size' => $media->size,
            ]);
        }
        return [
            'id' => $post->id,
            'caption' => $post->caption,
            'location' => $post->location,
            'media_type' => $post->media_type,
            'can_comment' => $post->can_comment,
            'media' => $medias,
        ];
    }
}
