<?php

namespace App\Http\Controllers\Api;

use App\Models\Story;
use App\Services\SpamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StorySpamController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function spamStory(Story $story, Request $request): JsonResponse
    {
        SpamService::spamStory($story, $request);
        return $this->respondWithArray([
                'success' => true,
                'message' => 'Spam successful'
            ]
        );
    }
}
