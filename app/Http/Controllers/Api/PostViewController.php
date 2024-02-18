<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use App\Transformers\UserSimpleTransformer;
use Auth;
use Illuminate\Http\JsonResponse;

class PostViewController extends ApiBaseController
{
    /**
     * List post views
     */
    public function views(Post $post): JsonResponse
    {
        $users = $post->views->pluck('user');

        return $this->respondWithCollection($users, new UserSimpleTransformer());
    }

    /**
     * View a post
     */
    public function view(Post $post): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $existingView = $post->views()->where('user_id', $user->id)->first();

        if (! $existingView) {
            $postView = new PostView();
            $postView->user()->associate($user);
            $postView->post()->associate($post);
            $postView->save();
        }

        return $this->respondWithMessage('View success');
    }
}
