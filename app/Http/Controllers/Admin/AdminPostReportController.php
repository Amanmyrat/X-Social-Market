<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostReport\PostReportListRequest;
use App\Http\Resources\Admin\PostReport\PostReportResource;
use App\Http\Resources\Admin\PostReport\PostReportUserResource;
use App\Models\Post;
use App\Services\Admin\PostReportService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminPostReportController extends Controller
{
    public function __construct(protected PostReportService $service)
    {
    }

    /**
     * Post report list
     */
    public function list(PostReportListRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();
        $limit = $validated['limit'] ?? 10;
        $query = $request->get('search_query') ?? null;
        $sort = $validated['sort'] ?? null;

        $posts = $this->service->list($limit, $query, $sort);

        return PostReportResource::collection($posts);
    }

    /**
     * Post reported users
     */
    public function reportUsers(Post $post): AnonymousResourceCollection
    {
        $users = $this->service->getUsersWhoReportedPost($post);

        return PostReportUserResource::collection($users);
    }
}
