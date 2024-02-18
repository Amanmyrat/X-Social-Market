<?php

namespace App\Services;

use App\Models\User;
use Auth;

class FollowerService
{
    public static function follow($followed_user_id): void
    {
        $following = User::find($followed_user_id);

        Auth::user()->followings()->syncWithoutDetaching($following);
    }

    public static function unfollow($following_id): void
    {
        $following = User::find($following_id);
        Auth::user()->followings()->detach($following);
    }
}
