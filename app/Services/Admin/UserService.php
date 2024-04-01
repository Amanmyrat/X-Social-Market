<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\UserProfile;
use App\Traits\SortableTrait;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Throwable;

class UserService
{
    use SortableTrait;

    public function list(string $type, int $limit, ?string $search_query = null, ?string $sort = null): LengthAwarePaginator
    {
        $query = User::where('type', $type)->when(isset($search_query), function ($query) use ($search_query) {
            $search_query = '%' . $search_query . '%';

            return $query->whereHas('profile', function ($q) use ($search_query) {
                $q->where('full_name', $search_query);
            })->orWhere('phone', 'LIKE', $search_query)->orWhere('username', 'LIKE', $search_query);
        });

        $this->applySorting($query, $sort, ['username', 'is_active', 'created_at']);

        return $query->paginate($limit);
    }

    /**
     * @throws Throwable
     */
    public function updateWithProfile(User $user, array $data): User
    {
        $user->update($data);
        if (isset($data['profile'])) {
            DB::transaction(function () use ($data, $user) {

                if (isset($data['profile']['profile_image'])) {
                    $profileImage = $data['profile']['profile_image'];
                }
                unset($data['profile']['profile_image']);

                if ($user->profile) {
                    $user->profile()->update($data['profile']);
                } else {
                    $data['profile'] = count($data['profile']) > 0 ? $data['profile'] : ['bio' => ''];

                    UserProfile::create(array_merge($data['profile'], ['user_id' => $user->id]));
                }

                $user->load('profile');

                if (isset($profileImage)) {
                    if ($user->profile->hasMedia()) {
                        $user->profile->clearMediaCollection('user_images');
                    }
                    $user->profile->addMedia($profileImage)->toMediaCollection('user_images');
                }
            });
        }

        return $user;
    }
}
