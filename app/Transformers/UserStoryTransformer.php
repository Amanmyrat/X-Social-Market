<?php

namespace App\Transformers;

use App\Models\User;
use App\Models\UserProfile;
use JetBrains\PhpStorm\ArrayShape;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class UserStoryTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'profile', 'stories',
    ];

    #[ArrayShape(['id' => 'mixed', 'phone' => 'mixed', 'username' => 'mixed', 'type' => 'mixed', 'last_activity' => 'mixed'])]
    public function transform(User $user): array
    {
        return [
            'id' => $user['id'],
            'phone' => $user['phone'],
            'username' => $user['username'],
            'type' => $user['type'],
            'last_activity' => $user['last_activity'],
        ];
    }

    public function includeProfile(User $user): ?Item
    {
        $profile = UserProfile::where('user_id', $user['id'])->get()->first();

        if ($profile) {
            return $this->item($profile, new UserProfileSmallTransformer());
        }

        return null;
    }

    public function includeStories(User $user): Collection
    {
        return $this->collection($user->stories, new StoryTransformer());
    }
}
