<?php

namespace App\Services\Statistics;

use App\Models\Follower;
use App\Models\Post;
use App\Models\PostBookmark;
use App\Models\PostFavorite;
use App\Models\PostView;
use App\Models\ProfileView;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PostStatisticsService extends BaseStatisticsService
{
    public function get($userId, $period): array
    {
        $startDate = $this->getStartDateForPeriod($period);

        $activePosts = Post::where('user_id', $userId)->where('is_active', true)->count();
        return [
            'active_posts_count' => $activePosts,
            'most_viewed_post' => $this->getMostViewedPost($userId, $startDate),
            'most_favorited_post' => $this->getMostFavoritedPost($userId, $startDate),
            'most_bookmarked_post' => $this->getMostBookmarkedPost($userId, $startDate),
        ];
    }

    protected function calculateEngagedUsersCount($postId, $startDate) {
        $queries = [];

        foreach (['post_favorites', 'post_comments', 'post_bookmarks', 'post_ratings'] as $table) {
            $query = DB::table($table)->where('post_id', $postId)->select('user_id');
            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }
            $queries[] = $query;
        }

        $unionQuery = array_shift($queries);

        foreach ($queries as $query) {
            $unionQuery = $unionQuery->unionAll($query);
        }

        return $unionQuery->get()->unique()->count();
    }

    protected function baseEngagementQuery($userId, $startDate, $relation): Builder|Post
    {
        return Post::where('user_id', $userId)
            ->withCount([$relation => function ($query) use ($startDate) {
                if ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }
            }])
            ->with(['media' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy("{$relation}_count", 'desc');
    }

    protected function getMostViewedPost($userId, $startDate): ?array
    {
        $post = $this->baseEngagementQuery($userId, $startDate, 'views')->first();
        if (!$post) return null;

        $activeUsersCount = $this->calculateEngagedUsersCount($post->id, $startDate);

        return [
            'id' => $post->id,
            'caption' => $post->caption,
            'view_count' => $post->views_count,
            'active_users_count' => $activeUsersCount,
            'media_type' => $post->media_type,
            'media' => $post->first_image_urls,
        ];
    }

    protected function getMostFavoritedPost($userId, $startDate): ?array
    {
        $post = $this->baseEngagementQuery($userId, $startDate, 'favorites')->withCount('comments')->first();

        return $post ? [
            'id' => $post->id,
            'caption' => $post->caption,
            'favorites_count' => $post->favorites_count,
            'comments_count' => $post->comments_count,
            'media_type' => $post->media_type,
            'media' => $post->first_image_urls,
        ] : null;
    }

    protected function getMostBookmarkedPost($userId, $startDate): ?array
    {
        $post = $this->baseEngagementQuery($userId, $startDate, 'bookmarks')->withCount('views')->first();

        return $post ? [
            'id' => $post->id,
            'caption' => $post->caption,
            'bookmarks_count' => $post->bookmarks_count,
            'view_count' => $post->views_count,
            'media_type' => $post->media_type,
            'media' => $post->first_image_urls,
        ] : null;
    }

}
