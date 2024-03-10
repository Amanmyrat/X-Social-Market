<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserReport\UserReportResource;
use App\Http\Resources\Admin\UserReport\UserReportUserResource;
use App\Models\User;
use App\Services\Admin\UserReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminUserReportController extends Controller
{
    public function __construct(protected UserReportService $service)
    {
    }

    /**
     * User report list
     */
    public function list(Request $request): AnonymousResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $users = $this->service->list($limit, $query);

        return UserReportResource::collection($users);
    }

    /**
     * User reported users
     */
    public function reportUsers(User $user): AnonymousResourceCollection
    {
        $users = $this->service->getUsersWhoReportedUser($user);

        return UserReportUserResource::collection($users);
    }
}
