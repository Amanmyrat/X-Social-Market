<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ReportRequest;
use App\Models\Story;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class StoryReportController
{
    public function __construct(protected ReportService $service)
    {
    }

    /**
     * Report story
     */
    public function reportStory(Story $story, ReportRequest $request): JsonResponse
    {
        $this->service->reportStory($story, $request->validated());

        return new JsonResponse([
            'success' => true,
            'message' => 'Report successful',
        ]);
    }
}
