<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Api\ApiBaseController;
use App\Models\User;
use App\Services\UserService;
use App\Transformers\UserListTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends ApiBaseController
{
    public function __construct(protected UserService $service)
    {
        parent::__construct();
    }

    /**
     * Users list
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;
        $type = $request->type ?? User::TYPE_USER;

        $brands = $this->service->list($type, $limit, $query);
        return $this->respondWithPaginator($brands, new UserListTransformer($type == User::TYPE_SELLER));
    }

}
