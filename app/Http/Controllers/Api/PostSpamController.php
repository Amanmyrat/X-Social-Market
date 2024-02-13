<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\SpamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostSpamController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function spamPost(Post $post, Request $request): JsonResponse
    {
        SpamService::spamPost($post, $request);

        return $this->respondWithArray([
            'success' => true,
            'message' => 'Spam successful',
        ]
        );
    }
}
