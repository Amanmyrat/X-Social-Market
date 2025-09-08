<?php

namespace Database\Seeders;

use App\Enum\AdminRole;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdmin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'super_admin@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        // Assign the "super-admin" role to this user
        $superAdmin->assignRole(AdminRole::SUPER_ADMIN->value);

    }
}
