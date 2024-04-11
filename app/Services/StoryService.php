<?php

namespace App\Services;

use App\Models\Story;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Exception;
use Throwable;

class StoryService
{
    /**
     * @throws Exception
     * @throws Throwable
     */
    public function create(array $validated, User $user): void
    {
        DB::transaction(function () use ($validated, $user) {

            $isActive = $validated['type'] === 'post';
            if (! $isActive && $user->type === User::TYPE_SELLER) {
                $activePostsCount = $user->posts()->where('is_active', true)->count();
                $isActive = $activePostsCount >= 10;
            }

            $storyData = array_merge($validated, [
                'user_id' => $user->id,
                'valid_until' => Carbon::now()->addYear(),
                'is_active' => $isActive,
            ]);

            $story = Story::create($storyData);

            if ($validated['type'] == 'basic') {
                $story->addMedia($validated['image'])->toMediaCollection('story_images');
            }
        });
    }
}
