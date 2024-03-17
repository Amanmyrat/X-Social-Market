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

            $story = Story::create(array_merge($validated, [
                'user_id' => $user->id,
                'valid_until' => Carbon::now()->addYear(),
                'image' => 'null'
            ]));

            $story->addMedia($validated['image'])->toMediaCollection('story_images');
        });
    }
}
