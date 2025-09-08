<?php

namespace App\Traits;

use App\DTOs\UserPostInteractionsDTO;
use Illuminate\Support\Facades\Auth;

trait HandlesUserPostInteractions
{
    /**
     * Get the current user's favorite post IDs.
     */
    public function getUserFavoritePostIds(): array
    {
        return $this->getUserPostInteractionIds('favorites');
    }

    /**
     * Get the current user's bookmarked post IDs.
     */
    public function getUserBookmarkedPostIds(): array
    {
        return $this->getUserPostInteractionIds('bookmarks');
    }

    /**
     * Get the current user's viewed post IDs.
     */
    public function getUserViewedPostIds(): array
    {
        return $this->getUserPostInteractionIds('postViews');
    }

    /**
     * Get the current user's following users IDs.
     */
    public function getUserFollowingsIds(): array
    {
        $user = Auth::user();

        return $user ? $user->followings()->pluck('follow_user_id')->toArray() : [];
    }

    /**
     * Get the current user's following users IDs.
     */
    public function getUserStoryViewUserIds(): array
    {
        $user = Auth::user();

        return $user ? $user->followings()
            ->with(['stories' => function ($query) {
                $query->where('created_at', '>=', now()->subDay())
                    ->where('is_active', true);
            }])
            ->whereDoesntHave('stories.views', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->pluck('users.id')
            ->toArray() : [];
    }

    /**
     * General method to retrieve user post interaction IDs based on a given relationship.
     *
     * @param  string  $relationshipMethod  The method name of the user's post interaction relationship.
     */
    protected function getUserPostInteractionIds(string $relationshipMethod): array
    {
        $user = Auth::user();

        return $user ? $user->$relationshipMethod()->pluck('post_id')->toArray() : [];
    }

    /**
     * Creates and returns a UserPostInteractionsDTO with the current user's
     * post interaction data.
     */
    protected function getUserInteractionsDTO(): UserPostInteractionsDTO
    {
        return new UserPostInteractionsDTO(
            $this->getUserFavoritePostIds(),
            $this->getUserBookmarkedPostIds(),
            $this->getUserViewedPostIds()
        );
    }
}
