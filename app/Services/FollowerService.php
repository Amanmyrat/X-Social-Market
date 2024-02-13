<?php

namespace App\Services;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;

class FollowerService
{
    public static function follow(Request $request): void
    {
        $validated = $request->validate([
            'following_id' => ['required', 'integer', 'exists:'.User::class.',id', 'not_in:'.auth('sanctum')->user()->id],
        ]);

        $following = User::find($validated['following_id']);

        auth('sanctum')->user()->followings()->syncWithoutDetaching($following);

    }

    public static function unfollow(Request $request): void
    {
        $validated = $request->validate(
            [
                'following_id' => ['required', 'integer', 'exists:'.User::class.',id', 'exists:'.Follower::class.',following_user_id'],
            ]
        );
        $following = User::find($validated['following_id']);
        auth('sanctum')->user()->followings()->detach($following);
    }
}
