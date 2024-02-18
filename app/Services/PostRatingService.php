<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostRating;
use Illuminate\Http\Request;

class PostRatingService
{
    public static function addRating(Request $request, Post $post): void
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        $rating = PostRating::where(['user_id' => auth('sanctum')->user()->id, 'post_id' => $post->id])->first();

        if ($rating) {
            $rating->update($validated);
        } else {
            $rating = new PostRating();
            $rating->user()->associate(auth('sanctum')->user());
            $rating->post()->associate($post);
            $rating->rating = $validated['rating'];
            $rating->save();
        }
    }
}
