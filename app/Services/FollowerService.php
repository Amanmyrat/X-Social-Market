<?php

namespace App\Services;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Http\Request;

class FollowerService
{
    public static function follow($following_id): void
    {
        $following = User::find($following_id);

        auth('sanctum')->user()->followings()->syncWithoutDetaching($following);
    }

    public static function unfollow($following_id): void
    {
        $following = User::find($following_id);
        auth('sanctum')->user()->followings()->detach($following);
    }
}
