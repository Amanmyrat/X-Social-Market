<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostRating;
use App\Models\User;
use Auth;

class PostRatingService
{
    public function addRating($validated, Post $post, User $user): void
    {
        $rating = PostRating::where(['user_id' => Auth::id(), 'post_id' => $post->id])->first();

        if ($rating) {
            $rating->update($validated);
        } else {
            $rating = new PostRating();
            $rating->user()->associate($user);
            $rating->post()->associate($post);
            $rating->rating = $validated['rating'];
            $rating->save();

            NotificationService::createPostNotification($rating, $rating->post_id);

        }
    }
}
