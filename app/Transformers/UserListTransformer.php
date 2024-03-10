<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserListTransformer extends TransformerAbstract
{
    private bool $isBusiness;

    public function __construct(bool $isBusiness)
    {
        $this->isBusiness = $isBusiness;
    }

    public function transform(User $user): array
    {
        return $this->isBusiness ? [
            'id' => $user->id,
            'phone' => $user->phone,
            'username' => $user->username,
            'is_active' => $user->is_active,
            'full_name' => $user->profile?->full_name,
            'profile_image' => $user->profile?->profile_image ? url('uploads/user/profile/'.$user->profile?->profile_image) : null,
            'location' => $user->profile?->location->title,
            'category' => $user->profile?->category->title,
        ] : [
            'id' => $user->id,
            'phone' => $user->phone,
            'username' => $user->username,
            'is_active' => $user->is_active,
            'full_name' => $user->profile?->full_name,
            'profile_image' => $user->profile?->profile_image ? url('uploads/user/profile/'.$user->profile?->profile_image) : null,
            'last_activity' => $user->last_activity,
        ];
    }
}
