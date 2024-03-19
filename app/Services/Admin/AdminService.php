<?php

namespace App\Services\Admin;

use _PHPStan_156ee64ba\Nette\Neon\Exception;
use App\Enum\AdminRole;
use App\Enum\ErrorMessage;
use App\Models\Admin;
use App\Traits\SortableTrait;
use DB;
use Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Throwable;

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

    /**
     * @throws Throwable
     */
    public function create(array $data): void
    {
        DB::transaction(function () use ($data) {

            $data['password'] = Hash::make($data['password']);

            $admin = Admin::create($data);
            $admin->assignRole($data['role']);

            if ($data['role'] != AdminRole::SUPER_ADMIN->value){
                $admin->givePermissionTo($data['permissions']);
            }
            if (isset($data['profile_image'])) {
                $admin->clearMediaCollection('admin_images');
                $admin->addMedia($data['profile_image'])->toMediaCollection('admin_images');
            }
        });
    }

    /**
     * @throws \Exception
     */
    public function update(Admin $admin, array $data): Admin
    {
        $admin->update($data);

        if (isset($data['role'])) {
            $admin->roles()->detach();
            $admin->assignRole($data['role']);
        }

        if (isset($data['permissions'])) {
            $admin->permissions()->detach();
            $admin->givePermissionTo($data['permissions']);
        }

        try {
            if (isset($data['profile_image'])) {
                $admin->clearMediaCollection('admin_images');
                $admin->addMedia($data['profile_image'])->toMediaCollection('admin_images');
            }
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }

        return $admin;
    }
}
