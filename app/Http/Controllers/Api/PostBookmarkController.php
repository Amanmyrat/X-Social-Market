<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Services\PostBookmarkService;
use App\Traits\HandlesUserPostInteractions;
use App\Traits\PreparesPostQuery;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostBookmarkController extends ApiBaseController
{
    use HandlesUserPostInteractions, PreparesPostQuery;

    public function __construct(protected PostBookmarkService $service)
    {
        parent::__construct();
    }

    /**
     * Change bookmark
     */
    public function change(Post $post, Request $request): JsonResponse
    {
        $request->validate([
            'collection_id' => 'nullable|exists:bookmark_collections,id',
        ]);

        $message = $this->service->add($post, Auth::user(), $request->input('collection_id'));

        return $this->respondWithMessage($message);
    }
}
