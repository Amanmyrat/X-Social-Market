<?php

namespace App\Services;

use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Http\Request;

class UserBlockService
{
    /**
     * @param Request $request
     */
    public static function block(Request $request): void
    {
        $validated = $request->validate([
            'block_user_id' => ['required', 'integer', 'exists:' . User::class . ',id', 'not_in:' . auth()->user()->id],
        ]);

        $block = User::find($validated['block_user_id']);

        auth()->user()->blockedUsers()->syncWithoutDetaching($block);
    }

    /**
     * @param Request $request
     */
    public static function unblock(Request $request): void
    {
        $validated = $request->validate(
            [
                'block_user_id' => ['required', 'integer', 'exists:' . User::class . ',id', 'exists:' . BlockedUser::class . ',blocked_user_id'],
            ]
        );
        $block = User::find($validated['block_user_id']);
        auth()->user()->blockedUsers()->detach($block);
    }
}
