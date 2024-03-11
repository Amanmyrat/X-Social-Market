<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Traits\SortableTrait;
use Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminService
{
    use SortableTrait;

    public function list(int $limit, ?string $search_query = null, ?string $sort = null): LengthAwarePaginator
    {
        $query = Admin::when(isset($search_query), function ($query) use ($search_query) {
            $search_query = '%'.$search_query.'%';

            return $query->where('phone', 'LIKE', $search_query)
                ->orWhere('name', 'LIKE', $search_query)
                ->orWhere('surname', 'LIKE', $search_query)
                ->orWhere('email', 'LIKE', $search_query);
        });

        $this->applySorting($query, $sort, ['name', 'email', 'is_active', 'last_activity']);

        return $query->paginate($limit);
    }

    public function create(array $data): Admin
    {
        if (isset($data['profile_image'])) {
            $profileImageName = $data['email'].'-'.time().'.'.$data['profile_image']->getClientOriginalExtension();
            $data['profile_image']->move(public_path('uploads/admin/'), $profileImageName);
            $data['profile_image'] = $profileImageName;
        }
        $data['password'] = Hash::make($data['password']);

        $admin = Admin::create($data);
        $admin->assignRole($data['role']);
        $admin->givePermissionTo($data['permissions']);

        return $admin;
    }

    public function update(Admin $admin, array $data): Admin
    {
        if (isset($data['profile_image'])) {
            $profileImageName = $admin->email.'-'.time().'.'.$data['profile_image']->getClientOriginalExtension();
            $data['profile_image']->move(public_path('uploads/admin/'), $profileImageName);
            $data['profile_image'] = $profileImageName;
        }

        $admin->update($data);

        if (isset($data['role'])) {
            $admin->roles()->detach();
            $admin->assignRole($data['role']);
        }

        if (isset($data['permissions'])) {
            $admin->permissions()->detach();
            $admin->givePermissionTo($data['permissions']);
        }

        return $admin;
    }
}
