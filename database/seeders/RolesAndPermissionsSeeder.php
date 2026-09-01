<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view dashboard',

            // Users
            'manage users', 'view users', 'create users', 'edit users', 'delete users',

            // Roles
            'manage roles', 'view roles', 'create roles', 'edit roles', 'delete roles',

            // Permissions
            'manage permissions', 'view permissions', 'create permissions', 'edit permissions', 'delete permissions',

            // Pages
            'manage pages', 'view pages', 'create pages', 'edit pages', 'delete pages',

            // Sliders
            'manage sliders', 'view sliders', 'create sliders', 'edit sliders', 'delete sliders',

            // Menus
            'manage menus', 'view menus', 'create menus', 'edit menus', 'delete menus',

            // Settings
            'manage settings', 'view settings', 'edit settings',

            // Media
            'manage medias', 'delete medias',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Admin — full access
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::where('guard_name', 'web')->get());

        // Manager — everything except user/role/permission management
        $managerPermissions = Permission::where('guard_name', 'web')
            ->where('name', 'not like', '%users%')
            ->where('name', 'not like', '%roles%')
            ->where('name', 'not like', '%permissions%')
            ->get();

        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions($managerPermissions);

        // Editor — content only (pages, sliders, menus, media)
        $editorPermissions = Permission::where('guard_name', 'web')
            ->where(function ($q) {
                $q->where('name', 'like', '%pages%')
                  ->orWhere('name', 'like', '%sliders%')
                  ->orWhere('name', 'like', '%menus%')
                  ->orWhere('name', 'like', '%medias%')
                  ->orWhere('name', 'view dashboard');
            })
            ->get();

        $editor = Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']);
        $editor->syncPermissions($editorPermissions);
    }
}
