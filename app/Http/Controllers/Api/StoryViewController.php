<?php

namespace App\Http\Controllers\Api;

use App\Models\Story;
use App\Services\StoryViewService;
use App\Transformers\UserSimpleTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;

class StoryViewController extends ApiBaseController
{
    /**
     * List story views
     *
     * @param Story $story
     * @return JsonResponse
     */
    public function views(Story $story): JsonResponse
    {
        return $this->respondWithCollection($story->views->pluck('user'), new UserSimpleTransformer());
    }

    /**
     * View a story
     *
     * @param Story $story
     * @return JsonResponse
     */
    public function view(Story $story): JsonResponse
    {
        $message = StoryViewService::addView($story);
        return $this->respondWithMessage($message);
    }
}
