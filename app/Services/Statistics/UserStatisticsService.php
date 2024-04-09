<?php

namespace App\Services\Statistics;

use App\Models\Follower;
use App\Models\Post;
use App\Models\PostView;
use App\Models\ProfileView;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Http\Resources\Statistics\UserStatisticsResource;
use Illuminate\Support\Carbon;

class UserStatisticsService extends BaseStatisticsService
{
    public function get(string $period): array
    {
        $userId = Auth::id();
        $startDate = $this->getStartDateForPeriod($period);

        return [
            'profileViewsCount' => $this->getProfileViewsCount($startDate),
            'postEngagementsCount' => $this->getPostEngagementsCount($userId, $startDate),
            'newFollowersCount' => $this->getNewFollowersCount($userId, $startDate),
            'postCount' => $this->getPostCount($userId, $startDate),
            'bestPost' => $this->getBestPost($userId, $startDate),
        ];
    }

    protected function getStartDateForPeriod($period): ?Carbon
    {
        return match ($period) {
            '1d' => now()->subDay(),
            '10d' => now()->subDays(10),
            '1m' => now()->subMonth(),
            '6m' => now()->subMonths(6),
            '1y' => now()->subYear(),
            'all' => null,
            default => now(),
        };
    }

    protected function getProfileViewsCount($startDate): int
    {
        $query = ProfileView::where('user_profile_id', Auth::user()->profile->id);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getPostEngagementsCount($userId, $startDate): int
    {
        $posts = Post::where('user_id', $userId)
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->get();

        $uniqueUserIds = collect();

        foreach ($posts as $post) {
            $favorites = DB::table('post_favorites')->where('post_id', $post->id)->select('user_id');
            $comments = DB::table('post_comments')->where('post_id', $post->id)->select('user_id');
            $bookmarks = DB::table('post_bookmarks')->where('post_id', $post->id)->select('user_id');
            $ratings = DB::table('post_ratings')->where('post_id', $post->id)->select('user_id');

            $engagementUserIds = $favorites
                ->unionAll($comments)
                ->unionAll($bookmarks)
                ->unionAll($ratings)
                ->pluck('user_id');

            $uniqueUserIds = $uniqueUserIds->merge($engagementUserIds);
        }

        return $uniqueUserIds->unique()->count();
    }


    protected function getNewFollowersCount($userId, $startDate): int
    {
        $query = Follower::where('followed_user_id', $userId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getPostCount($userId, $startDate): int
    {
        $query = Post::where('user_id', $userId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return $query->count();
    }

    protected function getBestPost($userId, $startDate): Post|null
    {
        $bestPostId = PostView::whereHas('post', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
                ->when($startDate, function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                })
                ->select(['post_id', DB::raw('COUNT(*) as total_views')])
                ->groupBy('post_id')
                ->orderByDesc('total_views')
                ->first()
                ->post_id ?? null;

        if (!$bestPostId) {
            return null;
        }

        /** @var Post $bestPost */
        $bestPost = Post::with('media')->where('id', $bestPostId)->first();

        // Calculate unique engaged users
        $engagedUsersCount = DB::table('post_favorites')
            ->select('user_id')
            ->where('post_id', $bestPostId)
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->union(
                DB::table('post_comments')
                    ->select('user_id')
                    ->where('post_id', $bestPostId)
                    ->when($startDate, function ($query) use ($startDate) {
                        $query->where('created_at', '>=', $startDate);
                    })
            )
            ->union(
                DB::table('post_bookmarks')
                    ->select('user_id')
                    ->where('post_id', $bestPostId)
                    ->when($startDate, function ($query) use ($startDate) {
                        $query->where('created_at', '>=', $startDate);
                    })
            )
            ->union(
                DB::table('post_ratings')
                    ->select('user_id')
                    ->where('post_id', $bestPostId)
                    ->when($startDate, function ($query) use ($startDate) {
                        $query->where('created_at', '>=', $startDate);
                    })
            );
        $engagedUsersCount = DB::query()->fromSub($engagedUsersCount, 'engaged_users')
            ->distinct()
            ->count();

        $bestPost->view_count = PostView::where('post_id', $bestPostId)
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->count();

        $bestPost->engaged_users_count = $engagedUsersCount;

        return $bestPost;
    }

}
