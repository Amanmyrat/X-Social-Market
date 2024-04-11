<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Admin\Story\StoryResource;
use App\Models\Story;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class AdminStoryController
{
    /**
     * Non active comments list
     */
    public function list(Request $request): AnonymousResourceCollection
    {
        $limit = $request->get('limit') ?? 10;

        $comments = Story::where('is_active', false)
            ->whereNull('blocked_at')
            ->with(['user', 'post'])
            ->latest()
            ->paginate($limit);

        return StoryResource::collection($comments);
    }

    /**
     * Accept comment
     *
     * @throws Throwable
     */
    public function accept(Story $story): JsonResponse
    {
        $story->update(['is_active' => true]);

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully accepted story',
        ]);
    }

    /**
     * Decline story
     *
     * @throws Throwable
     */
    public function decline(Story $story, Request $request): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $story->update(
            [
                'is_active' => false,
                'blocked_at' => now(),
                'block_reason' => $request->get('reason'),
            ]
        );

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully declined story',
        ]);
    }
}
