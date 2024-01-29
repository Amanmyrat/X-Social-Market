<?php

namespace App\Transformers;

use App\Models\UserProfile;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'profile'
    ];

    public function transform($user): array
    {
        $result = [
            'id' => $user['id'],
            'phone' => $user['phone'],
            'username' => $user['username'],
            'email' => $user['email'] ?? '',
            'type' => $user['type'],
            'last_activity' => $user['last_activity'],
        ];

        if(isset($user['token'])){
            $result['token'] = $user['token'];
        }

        return $result;
    }

    public function includeProfile($user): ?Item
    {
        $profile = UserProfile::with(['location', 'category'])->where('user_id', $user['id'])->get()->first();

        if($profile){
            return $this->item($profile, new UserProfileTransformer());
        }
        return null;
    }

}
