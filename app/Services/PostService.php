<?php

namespace App\Services;

use App\Http\Requests\PostRequest;
use App\Models\Post;

class PostService
{
    public static function create(PostRequest $request): void
    {
        $post = Post::create(array_merge($request->validated(), [
            'user_id' => $request->user()->id
        ]));

        $medias = $request->validated()['media_type'] == 'image'
            ? 'images'
            : 'videos';

        $post->addMultipleMediaFromRequest([$medias])
            ->each(function ($fileAdder) {

                $fileAdder->toMediaCollection();
            });

    }
}
