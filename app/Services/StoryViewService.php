<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostFavorite;
use App\Models\PostRating;
use App\Models\Story;
use App\Models\StoryView;
use Illuminate\Support\Collection;

class StoryViewService
{
    /**
     * @param Story $story
     * @return string
     */
    public static function addView(Story $story): string
    {
        $message = trans('notification.add_view_success');
        if (!$story->getIsViewed()) {
            $storyView = new StoryView();
            $storyView->user()->associate(auth()->user());
            $storyView->story()->associate($story);
            $storyView->save();
        }
        return $message;
    }

}
