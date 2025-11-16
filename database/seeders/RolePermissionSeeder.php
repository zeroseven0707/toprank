<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ====================================================
        // 📌 1️⃣ Define permissions per module
        // ====================================================
        // ====================================================
        // 📌 1️⃣ Define modules and actions
        // ====================================================
        $modules = [
            // ========================================================
            // 📊 DASHBOARD
            // ========================================================
            'dashboard' => ['view'],

            // ========================================================
            // 🔐 ACCESS CONTROL
            // ========================================================
            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'permissions' => ['view', 'create', 'edit', 'delete'],
            'assign-role' => ['create'], // assign user role/permission

            // ========================================================
            // 🗺️ MASTER DATA
            // ========================================================
            'cities' => ['view', 'create', 'edit', 'delete'],
            'content-categories' => ['view', 'create', 'edit', 'delete'],
            'tags' => ['view', 'create', 'delete'],

            // ========================================================
            // 🧩 CONTENT MANAGEMENT
            // ========================================================
            'sections' => ['view', 'create', 'edit', 'delete', 'reorder'],
            'url-crawls' => ['view', 'create', 'edit', 'delete', 'run'],
            'data-crawling' => ['view', 'create', 'show'],
            'content' => ['view', 'create', 'edit', 'delete'],

            // ========================================================
            // 📰 BLOG MANAGEMENT
            // ========================================================
            'blog-categories' => ['view', 'create', 'edit', 'delete'],
            'blogs' => ['view', 'create', 'edit', 'delete'],

            // ========================================================
            // ⚙️ SYSTEM SETTINGS
            // ========================================================
            'general-settings' => ['view', 'edit'],

            // ========================================================
            // 🔒 LEGAL & POLICY
            // ========================================================
            'privacy-policy' => ['view', 'edit'],
        ];

        $allPermissions = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permName = "{$action} {$module}";
                Permission::firstOrCreate(['name' => $permName]);
                $allPermissions[] = $permName;
            }
        }

        // ====================================================
        // 📌 2️⃣ Define roles and assign permissions
        // ====================================================
        $rolesWithPermissions = [
            'Super Admin' => $allPermissions, // semua akses
            'Admin' => ['view roles', 'create roles', 'edit roles', 'delete roles', 'view permissions', 'create permissions', 'edit permissions', 'delete permissions', 'view users', 'create users', 'edit users', 'delete users', 'view blogs', 'create blogs', 'edit blogs', 'delete blogs', 'view blog-categories', 'create blog-categories', 'edit blog-categories', 'delete blog-categories'],
            'Editor' => ['view blogs', 'create blogs', 'edit blogs', 'delete blogs', 'view blog-categories', 'create blog-categories', 'edit blog-categories', 'delete blog-categories', 'view privacy-policy', 'edit privacy-policy'],
            'User' => ['view blogs'],
        ];

        foreach ($rolesWithPermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions);
        }
        // ====================================================
        // 📌 3️⃣ Create default users and assign roles
        // ====================================================
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role' => 'Super Admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin',
            ],
            [
                'name' => 'Editor',
                'email' => 'editor@example.com',
                'password' => Hash::make('password'),
                'role' => 'Editor',
            ],
            [
                'name' => 'User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'User',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(['email' => $data['email']], ['name' => $data['name'], 'password' => $data['password']]);

            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }

        // ====================================================
        // ✅ Done
        // ====================================================
        $this->command->info('✅ Roles, Permissions, and Default Users seeded successfully!');
    }
}
