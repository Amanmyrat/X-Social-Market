<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\UserProfile;
use App\Traits\SortableTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    use SortableTrait;

    public function list(string $type, int $limit, ?string $search_query = null, ?string $sort = null): LengthAwarePaginator
    {
        $query = User::where('type', $type)->when(isset($search_query), function ($query) use ($search_query) {
            $search_query = '%'.$search_query.'%';

            return $query->whereHas('profile', function ($q) use ($search_query) {
                $q->where('full_name', $search_query);
            })->orWhere('phone', 'LIKE', $search_query)->orWhere('username', 'LIKE', $search_query);
        });

        $this->applySorting($query, $sort, ['username', 'is_active', 'created_at']);

        return $query->paginate($limit);
    }

    public function updateWithProfile(User $user, array $data): User
    {
        if (isset($data['profile']['profile_image'])) {
            $profileImageName = $user->phone.'-'.time().'.'.$data['profile']['profile_image']->getClientOriginalExtension();
            $data['profile']['profile_image']->move(public_path('uploads/user/profile'), $profileImageName);
            $data['profile']['profile_image'] = $profileImageName;
        }
        $user->update($data);

        if (isset($data['profile'])) {
            if ($user->profile) {
                $user->profile()->update($data['profile']);
            } else {
                UserProfile::create(array_merge($data['profile'], ['user_id' => $user->id]));
            }
        }

        return $user;
    }
}
