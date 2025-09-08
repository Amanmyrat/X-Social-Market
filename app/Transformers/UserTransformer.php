<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
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

        if (isset($user['token'])) {
            $result['token'] = $user['token'];
        }

        return $result;
    }
}
