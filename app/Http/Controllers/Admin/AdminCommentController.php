<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\PostComment\PostCommentResource;
use App\Models\PostComment;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class AdminCommentController
{
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

        NotificationService::createPostInteractionNotificationToPostAuthor($comment, $comment->post_id);

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

        NotificationService::createCommentRejectNotificationToCommentCreator($comment, $comment->id, $request->get('reason'));

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully declined comment',
        ]);
    }
}
