<?php

namespace App\Traits;

use App\DTOs\UserPostInteractionsDTO;
use Illuminate\Support\Facades\Auth;

trait HandlesUserPostInteractions
{
    /**
     * Get the current user's favorite post IDs.
     *
     * @return array
     */
    public function getUserFavoritePostIds(): array
    {
        return $this->getUserPostInteractionIds('favorites');
    }

    /**
     * Get the current user's bookmarked post IDs.
     *
     * @return array
     */
    public function getUserBookmarkedPostIds(): array
    {
        return $this->getUserPostInteractionIds('bookmarks');
    }

    /**
     * Get the current user's viewed post IDs.
     *
     * @return array
     */
    public function getUserViewedPostIds(): array
    {
        return $this->getUserPostInteractionIds('postViews');
    }

    /**
     * General method to retrieve user post interaction IDs based on a given relationship.
     *
     * @param string $relationshipMethod The method name of the user's post interaction relationship.
     * @return array
     */
    protected function getUserPostInteractionIds(string $relationshipMethod): array
    {
        $user = Auth::user();
        return $user ? $user->$relationshipMethod()->pluck('post_id')->toArray() : [];
    }

    /**
     * Creates and returns a UserPostInteractionsDTO with the current user's
     * post interaction data.
     *
     * @return UserPostInteractionsDTO
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
