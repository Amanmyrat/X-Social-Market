<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ReportRequest;
use App\Models\Post;
use App\Services\ReportService;
use Auth;
use Illuminate\Http\JsonResponse;

class PostReportController
{
    public function __construct(protected ReportService $service)
    {
    }

    /**
     * Report a post
     */
    public function reportPost(Post $post, ReportRequest $request): JsonResponse
    {
        $this->service->reportPost($post, $request->validated(), Auth::id());

        return new JsonResponse([
            'success' => true,
            'message' => 'Report successful',
        ]);
    }
}
