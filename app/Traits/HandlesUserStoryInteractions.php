<?php

namespace App\Traits;

use Auth;

trait HandlesUserStoryInteractions
{
    /**
     * Get the current user's viewed story IDs, ensuring the stories are valid.
     */
    public function getUserViewedStoryIds(): array
    {
        $user = Auth::user();

        return $user->storyViews()
            ->join('stories', 'story_views.story_id', '=', 'stories.id')
            ->where('stories.valid_until', '>', now())
            ->pluck('stories.id')
            ->toArray();
    }

    /**
     * Get the current user's favorite story IDs, ensuring the stories are valid.
     */
    public function getUserFavoriteStoryIds(): array
    {
        $user = Auth::user();

        return $user->storyFavorites()
            ->join('stories', 'story_favorites.story_id', '=', 'stories.id')
            ->where('stories.valid_until', '>', now())
            ->pluck('stories.id')
            ->toArray();
    }
}
