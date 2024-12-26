<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\NotificationResource;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class NotificationController
{
    /**
     * User notifications list for 7 days
     */
    public function  list(): JsonResponse
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->with([
                'initiator.profile.media',
                'post.media',
                'story.media'
            ])
            ->orderByDesc('created_at')
            ->get();

//        $notifications = $user->notifications()
//            ->where('created_at', '>=', Carbon::now()->subDays(7))
//            ->orderByDesc('created_at')
//            ->get();
//
//        $notifications->load(['initiator.profile.media', 'post.media', 'story.media']);


        $user->notifications()
            ->whereIn('id', $notifications->pluck('id'))
            ->update(['is_read' => true]);

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
                return NotificationResource::collection($dayNotifications);
            }),
        ]);
    }

    /**
     * Users unread notifications
     */
    public function unreadCount(): JsonResponse
    {
        $user = Auth::user();

        $count = $user->notifications()
            ->where('is_read', false)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->count();

        return response()->json(['unreadCount' => $count]);
    }
}
