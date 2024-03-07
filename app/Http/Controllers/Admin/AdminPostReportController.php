<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PostReport\PostReportResource;
use App\Http\Resources\Admin\PostReport\PostReportUserResource;
use App\Models\Post;
use App\Services\Admin\PostReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminPostReportController extends Controller
{
    public function __construct(protected PostReportService $service)
    {
    }

    /**
     * Post report list
     */
    public function list(Request $request): AnonymousResourceCollection
    {
        $limit = $request->limit ?? 10;
        $query = $request->search_query ?? null;

        $posts = $this->service->list($limit, $query);

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
