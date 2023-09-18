<?php

namespace App\Transformers;

use App\Models\UserProfile;
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
            'last_login' => $user['last_login'],
        ];

        if(isset($user['token'])){
            $result['token'] = $user['token'];
        }

        return $result;
    }

    public function includeProfile($user)
    {
        $profile = UserProfile::where('user_id', $user['id'])->get()->first();

        if($profile){
            return $this->item($profile, new UserProfileTransformer());
        }
    }

}
