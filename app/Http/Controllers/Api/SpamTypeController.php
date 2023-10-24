<?php

namespace App\Http\Controllers\Api;

use App\Models\SpamType;
use App\Services\SpamService;
use App\Transformers\SpamTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpamTypeController extends ApiBaseController
{
    /**
     * Create spam type
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        SpamService::create($request);
        return $this->respondWithArray([
                'success' => true,
                'message' => 'Successfully created a new spam type'
            ]
        );
    }

    /**
     * Spam types list
     * @return JsonResponse
     */
    public function types(): JsonResponse
    {
        return $this->respondWithCollection(SpamType::all(), new SpamTypeTransformer());
    }

}
