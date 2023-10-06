<?php

namespace App\Http\Controllers\Api;

use App\Services\StoryService;
use App\Transformers\StoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoryController extends ApiBaseController
{
    /**
     * Create story
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        StoryService::create($request);

        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully created a new story'
            ]
        );
    }

    /**
     * My stories list
     * @return JsonResponse
     */
    public function myStories(): JsonResponse
    {
        return $this->respondWithCollection(auth()->user()->stories, new StoryTransformer());
    }
}
