<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ReportRequest;
use App\Models\User;
use App\Services\ReportService;
use Auth;
use Illuminate\Http\JsonResponse;

class UserReportController
{
    public function __construct(protected ReportService $service)
    {
    }

    /**
     * Report user
     */
    public function reportUser(User $user, ReportRequest $request): JsonResponse
    {
        $this->service->reportUser($user, $request->validated(), Auth::id());

        return new JsonResponse([
            'success' => true,
            'message' => 'Report successful',
        ]);
    }
}
