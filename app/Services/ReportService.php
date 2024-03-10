<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostReport;
use App\Models\Story;
use App\Models\StoryReport;
use App\Models\User;
use App\Models\UserReport;

class ReportService
{
    public function reportPost(Post $post, array $validated, int $userId): void
    {
        $validated['post_id'] = $post->id;
        $validated['user_id'] = $userId;
        PostReport::create($validated);
    }

    public function reportStory(Story $story, array $validated, int $userId): void
    {
        $validated['story_id'] = $story->id;
        $validated['user_id'] = $userId;
        StoryReport::create($validated);
    }

    public function reportUser(User $user, array $validated, int $userId): void
    {
        $validated['reported_user_id'] = $user->id;
        $validated['user_id'] = $userId;
        UserReport::create($validated);
    }
}
