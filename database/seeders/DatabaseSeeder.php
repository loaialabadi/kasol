<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Admin;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            AddsTableSeeder::class,
            OffersTableSeeder::class,
            CategoriesTableSeeder::class,
            SubCategoriesTableSeeder::class,
            ProductsTableSeeder::class,
            FavoritesTableSeeder::class,
            StorySeeder::class,
            ServicesTableSeeder::class,
        ]);

        // $this->call(RolesAndPermissionsSeeder::class);

        // $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        // $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // $manageUsersPermission = Permission::firstOrCreate(['name' => 'manage users', 'guard_name' => 'admin']);
        // $viewPostsPermission = Permission::firstOrCreate(['name' => 'view posts', 'guard_name' => 'web']);

        // $adminRole->givePermissionTo($manageUsersPermission);
        // $userRole->givePermissionTo($viewPostsPermission);

        // $admin = Admin::firstOrCreate(
        //     ['id' => 1],
        //     ['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => bcrypt('password')]
        // );
        // $admin->assignRole('admin');

        // $user = User::firstOrCreate(
        //     ['id' => 1],
        //     ['name' => 'Normal User', 'email' => 'user@example.com', 'password' => bcrypt('password')]
        // );
        // $user->assignRole('user');
    }
}
