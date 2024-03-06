<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostReport;
use App\Models\ReportType;
use App\Models\Story;
use App\Models\StoryReport;
use Auth;
use Illuminate\Http\Request;

class ReportService
{
    public function reportPost(Post $post, array $validated): void
    {
        $validated['post_id'] = $post->id;
        $validated['user_id'] = Auth::id();
        PostReport::create($validated);
    }

    public function reportStory(Story $story, array $validated): void
    {
        $validated['story_id'] = $story->id;
        $validated['user_id'] = Auth::id();
        StoryReport::create($validated);
    }
}
