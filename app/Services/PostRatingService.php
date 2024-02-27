<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostRating;
use Auth;
use Illuminate\Http\Request;

class PostRatingService
{
    public static function addRating($validated, Post $post): void
    {
        $rating = PostRating::where(['user_id' => Auth::id(), 'post_id' => $post->id])->first();

        if ($rating) {
            $rating->update($validated);
        } else {
            $rating = new PostRating();
            $rating->user()->associate(Auth::user());
            $rating->post()->associate($post);
            $rating->rating = $validated['rating'];
            $rating->save();

            NotificationService::createPostNotification($rating, $rating->post_id);

        }
    }
}
