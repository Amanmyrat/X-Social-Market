<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminCreateRequest;
use App\Http\Requests\Admin\AdminDeleteRequest;
use App\Http\Requests\Admin\AdminListRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use App\Http\Resources\Admin\Admin\AdminResource;
use App\Http\Resources\Admin\Admin\AdminResourceCollection;
use App\Models\Admin;
use App\Services\Admin\AdminService;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminAdminController
{
    public function __construct(protected AdminService $service)
    {
    }

    /**
     * Admins list
     */
    public function list(AdminListRequest $request): AdminResourceCollection
    {
        $validated = $request->validated();
        $limit = $validated['limit'] ?? 10;
        $query = $validated['search_query'] ?? null;
        $sort = $validated['sort'] ?? null;

        $admins = $this->service->list($limit, $query, $sort);

        return new AdminResourceCollection($admins);
    }

    /**
     * Admin create
     */
    public function create(AdminCreateRequest $request): JsonResponse
    {
        $this->service->create($request->validated());

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully created a new admin',
        ]);
    }

    /**
     * Admin details
     */
    public function adminDetails(Admin $admin): AdminResource
    {
        return new AdminResource($admin, true);

    }

    /**
     * Update admin
     */
    public function update(Admin $admin, AdminUpdateRequest $request): AdminResource
    {
        $admin = $this->service->update($admin, $request->validated());

        return new AdminResource($admin, true);
    }

    /**
     * Delete admins
     */
    public function delete(AdminDeleteRequest $request): JsonResponse
    {
        Admin::whereIn('id', $request->admins)->delete();

        return new JsonResponse([
            'success' => true,
            'message' => 'Successfully deleted',
        ]);
    }

    /**
     * Admin roles
     */
    public function roles(): JsonResponse
    {
        $roles = Role::all(['id', 'name', 'display_name']);

        return new JsonResponse([
            'data' => $roles,
            'success' => true,
        ]);
    }

    /**
     * Admin permissions
     */
    public function permissions(): JsonResponse
    {
        $permissions = Permission::all(['id', 'name', 'display_name']);

        return new JsonResponse([
            'data' => $permissions,
            'success' => true,
        ]);
    }
}
