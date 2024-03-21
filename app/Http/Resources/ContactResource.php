<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->resource['user'];
        return [
            'phone' => $this->resource['phone'],
            'user' => $user ? [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'image' => $user['image'],
                'isFollowing' => $user['isFollowing'],
            ] : null,
        ];
    }
}
