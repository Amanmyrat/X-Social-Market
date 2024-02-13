<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\PostView;
use App\Transformers\UserSimpleTransformer;
use Illuminate\Http\JsonResponse;

class PostViewController extends ApiBaseController
{
    /**
     * List post views
     */
    public function views(Post $post): JsonResponse
    {
        return $this->respondWithCollection($post->views->pluck('user'), new UserSimpleTransformer());
    }

    /**
     * View a post
     */
    public function view(Post $post): JsonResponse
    {
        $message = trans('notification.add_view_success');
        if (! $post->getIsViewed()) {
            $postView = new PostView();
            $postView->user()->associate(auth('sanctum')->user());
            $postView->post()->associate($post);
            $postView->save();
        }

        return $this->respondWithMessage($message);
    }
}
