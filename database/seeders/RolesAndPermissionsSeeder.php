<?php

namespace Database\Seeders;

use App\Enum\AdminRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions with display names
        $permissions = [
            ['name' => 'manage-categories', 'display_name' => 'Kategoriýalary dolandyrmak', 'guard_name' => 'admin'],
            ['name' => 'manage-brands', 'display_name' => 'Markalary dolandyrmak', 'guard_name' => 'admin'],
            ['name' => 'manage-users', 'display_name' => 'Ulanyjylary dolandyrmak', 'guard_name' => 'admin'],
            ['name' => 'manage-posts', 'display_name' => 'Harytlary dolandyrmak', 'guard_name' => 'admin'],
            ['name' => 'manage-reports', 'display_name' => 'Reportlary dolandyrmak', 'guard_name' => 'admin'],
            ['name' => 'manage-options', 'display_name' => 'Haryt goşmaçalary dolandyrmak', 'guard_name' => 'admin'],
            ['name' => 'manage-inactive', 'display_name' => 'Moderatorlyk', 'guard_name' => 'admin'],
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'display_name' => $permission['display_name'],
                'guard_name' => $permission['guard_name'],
            ]);
        }

        // Roles with display names
        $roles = [
            ['name' => AdminRole::Admin->value, 'display_name' => 'Admin', 'guard_name' => 'admin'],
            ['name' => AdminRole::SUPER_ADMIN->value, 'display_name' => 'Super Admin', 'guard_name' => 'admin'],
        ];

        foreach ($roles as $role) {
            Role::create(
                [
                    'name' => $role['name'],
                    'display_name' => $role['display_name'],
                    'guard_name' => $role['guard_name'],
                ]
            );
        }

        // Assign all permissions to the super admin role
        $superAdminRole = Role::findByName('super-admin', 'admin');
        $superAdminRole->givePermissionTo(Permission::all());
    }
}
