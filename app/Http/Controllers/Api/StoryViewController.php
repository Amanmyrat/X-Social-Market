<?php

namespace App\Http\Controllers\Api;

use App\Models\Story;
use App\Models\StoryView;
use App\Models\User;
use App\Transformers\UserSimpleTransformer;
use Auth;
use Illuminate\Http\JsonResponse;

class StoryViewController extends ApiBaseController
{
    /**
     * List story views
     */
    public function views(Story $story): JsonResponse
    {
        return $this->respondWithCollection($story->views->pluck('user'), new UserSimpleTransformer());
    }

    /**
     * View a story
     */
    public function view(Story $story): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $existingView = $story->views()->where('user_id', $user->id)->first();

        if (! $existingView) {
            $storyView = new StoryView();
            $storyView->user()->associate($user);
            $storyView->story()->associate($story);
            $storyView->save();
        }

        return $this->respondWithMessage('View success');
    }
}
