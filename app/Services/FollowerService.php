<?php

namespace App\Services;

use App\Models\User;

class FollowerService
{
    public static function follow($followed_user_id): void
    {
        $following = User::find($followed_user_id);

        auth('sanctum')->user()->followings()->syncWithoutDetaching($following);
    }

    public static function unfollow($following_id): void
    {
        $following = User::find($following_id);
        auth('sanctum')->user()->followings()->detach($following);
    }
}
