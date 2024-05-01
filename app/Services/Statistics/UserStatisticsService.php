<?php

namespace App\Services\Statistics;

use App\Models\Follower;
use App\Models\Post;
use App\Models\PostView;
use App\Models\ProfileView;
use Auth;
use DB;

class UserStatisticsService extends BaseStatisticsService
{
    public function get(string $period): array
    {
        $userId = Auth::id();
        $startDate = $this->getStartDateForPeriod($period);

        return [
            'profileViewsCount' => $this->getProfileViewsCount($startDate, now()),
            'postEngagementsCount' => $this->getPostEngagementsCount($userId, $startDate, now()),
            'newFollowersCount' => $this->getNewFollowersCount($userId, $startDate, now()),
            'postCount' => $this->getPostCount($userId, $startDate, now()),
            'bestPost' => $this->getBestPost($userId, $startDate, now()),
        ];
    }

    public function getWithPrevious(string $period): array
    {
        $currentStats = $this->get($period);
        $startDate = $this->getStartDateForPeriod($period);
        $previousStartDate = $this->getPreviousStartDateForPeriod($period, $startDate);

        if ($period === 'all') {
            $previousStats = null;
        } else {
            $previousStats = [
                'profileViewsCount' => $this->getProfileViewsCount($previousStartDate, $startDate),
                'postEngagementsCount' => $this->getPostEngagementsCount(Auth::id(), $previousStartDate, $startDate),
                'newFollowersCount' => $this->getNewFollowersCount(Auth::id(), $previousStartDate, $startDate),
                'postCount' => $this->getPostCount(Auth::id(), $previousStartDate, $startDate),
                'bestPost' => $this->getBestPost(Auth::id(), $previousStartDate, $startDate),
            ];
        }

        return [
            'current' => $currentStats,
            'previous' => $previousStats
        ];
    }


    protected function getProfileViewsCount($startDate, $endDate): int
    {
        $query = ProfileView::where('user_profile_id', Auth::user()->profile->id);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<', $endDate);
        }
        return $query->count();
    }

    protected function getPostEngagementsCount($userId, $startDate, $endDate): int
    {
        // Use a single query to gather all unique user IDs from engagements within the date range.
        $engagements = DB::table('posts')
            ->where('posts.user_id', $userId)
            ->leftJoin('post_favorites', 'posts.id', '=', 'post_favorites.post_id')
            ->leftJoin('post_comments', 'posts.id', '=', 'post_comments.post_id')
            ->leftJoin('post_bookmarks', 'posts.id', '=', 'post_bookmarks.post_id')
            ->leftJoin('post_ratings', 'posts.id', '=', 'post_ratings.post_id')
            ->select('post_favorites.user_id as favorite_user_id', 'post_comments.user_id as comment_user_id', 'post_bookmarks.user_id as bookmark_user_id', 'post_ratings.user_id as rating_user_id')
            ->when($startDate, function ($query) use ($startDate) {
                $query->where(function ($q) use ($startDate) {
                    $q->where('post_favorites.created_at', '>=', $startDate)
                        ->orWhere('post_comments.created_at', '>=', $startDate)
                        ->orWhere('post_bookmarks.created_at', '>=', $startDate)
                        ->orWhere('post_ratings.created_at', '>=', $startDate);
                });
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('post_favorites.created_at', '<', $endDate);
            })
            ->get();

        // Flatten the list of user IDs from different columns and remove null values
        $userIds = $engagements->flatMap(function ($item) {
            return [$item->favorite_user_id, $item->comment_user_id, $item->bookmark_user_id, $item->rating_user_id];
        })->filter()->unique();

        return $userIds->count();
    }

    protected function getNewFollowersCount($userId, $startDate, $endDate): int
    {
        $query = Follower::where('follow_user_id', $userId)
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('created_at', '<', $endDate);
            });

        return $query->count();

    }

    protected function getPostCount($userId, $startDate, $endDate): int
    {
        $query = Post::where('user_id', $userId)
            ->when($startDate, function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('created_at', '<', $endDate);
            });

        return $query->count();
    }

    protected function getBestPost($userId, $startDate, $endDate): ?Post
    {
        $bestPostId = PostView::whereHas('post', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
                ->when($startDate, function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    $query->where('created_at', '<', $endDate);
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
