<?php

namespace App\Services;

use App\Models\Story;
use App\Models\StoryView;
use Auth;

class StoryViewService
{
    public static function addView(Story $story): string
    {
        $message = trans('notification.add_view_success');
        if (! $story->getIsViewed()) {
            $storyView = new StoryView();
            $storyView->user()->associate(Auth::user());
            $storyView->story()->associate($story);
            $storyView->save();
        }

        return $message;
    }
}
