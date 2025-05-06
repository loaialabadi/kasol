<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions with 'admin' guard
        $permissions = [
            'view admin dashboard',
            'manage users',
            'manage orders',
            'manage reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'admin']
            );
        }

        // Create roles with 'admin' guard
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'admin']
        );
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'admin']
        );
        $adminRole->givePermissionTo(['view admin dashboard', 'manage users']);
    }
}
