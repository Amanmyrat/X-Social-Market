<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostSpam;
use App\Models\SpamType;
use App\Models\Story;
use App\Models\StorySpam;
use Illuminate\Http\Request;

class SpamService
{
    /**
     * @param Request $request
     */
    public static function create(Request $request): void
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
        ]);
        SpamType::create($validated);
    }

    /**
     * @param Post $post
     * @param Request $request
     */
    public static function spamPost(Post $post, Request $request): void
    {
        $validated = $request->validate([
            'spam_type_id' => ['required', 'integer', 'exists:' . SpamType::class . ',id'],
            'message' => ['filled', 'string'],
        ]);
        $validated['post_id'] = $post->id;
        $validated['user_id'] = auth()->user()->id;
        PostSpam::create($validated);
    }

    /**
     * @param Story $story
     * @param Request $request
     */
    public static function spamStory(Story $story, Request $request): void
    {
        $validated = $request->validate([
            'spam_type_id' => ['required', 'integer', 'exists:' . SpamType::class . ',id'],
            'message' => ['filled', 'string'],
        ]);
        $validated['story_id'] = $story->id;
        $validated['user_id'] = auth()->user()->id;
        StorySpam::create($validated);
    }
}
