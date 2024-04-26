<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Story;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $storyCount = 1000;
        $defaultStoryCount = 700;
        $users = User::pluck('id');
        $posts = Post::pluck('id');

        for ($i = 0; $i < $storyCount; $i++) {
            $userId = $users->random();
            $postId = $posts->random();
            $type = $i < $defaultStoryCount ? 'default' : 'post';
            $isActive = rand(1, 10) <= 7;

            $story = Story::create([
                'user_id' => $userId,
                'post_id' => $type == 'post' ? $postId : null,
                'valid_until' => Carbon::now()->addYear(),
                'is_active' => $isActive,
                'blocked_at' => null,
                'block_reason' => null,
            ]);

            if ($type == 'default') {
                $imagePath = '/posts/' . rand(1, 5) . '.png';
                $story->addMediaFromDisk($imagePath, 'seeders')->toMediaCollection('story_images');
            }
        }
    }
}
