<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\PostComment\PostCommentResource;
use App\Models\PostComment;
use App\Services\Admin\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Throwable;

class AdminCommentController
{
    public function __construct(protected AdminService $service)
    {
    }

    /**
     * Non active comments list
     */
    public function list(Request $request): AnonymousResourceCollection
    {
        $limit = $request->get('limit') ?? 10;

        $comments = PostComment::where('is_active', false)
            ->whereNull('blocked_at')
            ->with(['user', 'post'])
            ->latest()
            ->paginate($limit);

        return PostCommentResource::collection($comments);
    }

    /**
     * Accept comment
     *
     * @throws Throwable
     */
    public function accept(PostComment $comment): JsonResponse
    {
        $comment->update(['is_active' => true]);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully accepted comment',
        ]);
    }

    /**
     * Decline comment
     *
     * @throws Throwable
     */
    public function decline(PostComment $comment, Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $comment->update(
            [
                'is_active' => false,
                'blocked_at' => now(),
                'block_reason' => $request->get('reason'),
            ]
        );

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully declined comment',
        ]);
    }
}
