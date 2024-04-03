<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PostNotificationResource;
use App\Models\PostNotification;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class NotificationController
{
    /**
     * User notifications list for 7 days
     */
    public function list(): JsonResponse
    {
        $userId = Auth::id();

        $notifications = PostNotification::whereHas('post', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->with(['post.user', 'post.media', 'notifiable'])
            ->orderByDesc('created_at')
            ->get();

        $notifications->each->update(['is_read' => true]);

        if ($notifications->isEmpty()) {
            return new JsonResponse([
                'data' => null,
            ]);
        }

        // Group by date
        $groupedNotifications = $notifications->groupBy(function ($date) {
            $createdAt = Carbon::parse($date->created_at);
            if ($createdAt->isToday()) {
                return 'today';
            } elseif ($createdAt->isYesterday()) {
                return 'yesterday';
            } else {
                return 'others';
            }
        });

        return new JsonResponse([
            'data' => $groupedNotifications->map(function ($dayNotifications) {
                return PostNotificationResource::collection($dayNotifications);
            }),
        ]);
    }

    /**
     * Users unread notifications
     */
    public function unreadCount(): JsonResponse
    {
        $userId = Auth::id();

        $count = PostNotification::whereHas('post', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('is_read', false)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        return response()->json(['unreadCount' => $count]);
    }
}
