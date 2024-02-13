<?php

namespace App\Services;

use App\Models\Story;
use App\Models\StoryView;

class StoryViewService
{
    public static function addView(Story $story): string
    {
        $message = trans('notification.add_view_success');
        if (! $story->getIsViewed()) {
            $storyView = new StoryView();
            $storyView->user()->associate(auth('sanctum')->user());
            $storyView->story()->associate($story);
            $storyView->save();
        }

        return $message;
    }
}
