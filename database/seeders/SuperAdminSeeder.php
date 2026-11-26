<?php

namespace Database\Seeders;

use App\Enums\RoleScope;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed SUPER_ADMIN permissions
        $permissions = [
            ['module' => 'organizations', 'name' => 'View Organizations', 'slug' => 'organizations.view', 'sort_order' => 10],
            ['module' => 'organizations', 'name' => 'Create Organization', 'slug' => 'organizations.create', 'sort_order' => 11],
            ['module' => 'organizations', 'name' => 'Update Organization', 'slug' => 'organizations.update', 'sort_order' => 12],
            ['module' => 'organizations', 'name' => 'Suspend Organization', 'slug' => 'organizations.suspend', 'sort_order' => 13],
            ['module' => 'organizations', 'name' => 'Approve Organization', 'slug' => 'organizations.approve', 'sort_order' => 14],

            ['module' => 'system_users', 'name' => 'View System Users', 'slug' => 'system_users.view', 'sort_order' => 20],
            ['module' => 'system_users', 'name' => 'Create System User', 'slug' => 'system_users.create', 'sort_order' => 21],
            ['module' => 'system_users', 'name' => 'Update System User', 'slug' => 'system_users.update', 'sort_order' => 22],
            ['module' => 'system_users', 'name' => 'Delete System User', 'slug' => 'system_users.delete', 'sort_order' => 23],

            ['module' => 'roles', 'name' => 'View Roles', 'slug' => 'roles.view', 'sort_order' => 30],
            ['module' => 'roles', 'name' => 'Create Role', 'slug' => 'roles.create', 'sort_order' => 31],
            ['module' => 'roles', 'name' => 'Update Role', 'slug' => 'roles.update', 'sort_order' => 32],
            ['module' => 'roles', 'name' => 'Delete Role', 'slug' => 'roles.delete', 'sort_order' => 33],

            // Global Masters
            ['module' => 'masters_categories', 'name' => 'View Categories', 'slug' => 'categories.view', 'sort_order' => 40],
            ['module' => 'masters_categories', 'name' => 'Create Category', 'slug' => 'categories.create', 'sort_order' => 41],
            ['module' => 'masters_categories', 'name' => 'Update Category', 'slug' => 'categories.update', 'sort_order' => 42],
            ['module' => 'masters_categories', 'name' => 'Delete Category', 'slug' => 'categories.delete', 'sort_order' => 43],

            ['module' => 'masters_units', 'name' => 'View Units', 'slug' => 'units.view', 'sort_order' => 50],
            ['module' => 'masters_units', 'name' => 'Create Unit', 'slug' => 'units.create', 'sort_order' => 51],
            ['module' => 'masters_units', 'name' => 'Update Unit', 'slug' => 'units.update', 'sort_order' => 52],
            ['module' => 'masters_units', 'name' => 'Delete Unit', 'slug' => 'units.delete', 'sort_order' => 53],

            ['module' => 'masters_products', 'name' => 'View Products', 'slug' => 'products.view', 'sort_order' => 60],
            ['module' => 'masters_products', 'name' => 'Create Product', 'slug' => 'products.create', 'sort_order' => 61],
            ['module' => 'masters_products', 'name' => 'Update Product', 'slug' => 'products.update', 'sort_order' => 62],
            ['module' => 'masters_products', 'name' => 'Delete Product', 'slug' => 'products.delete', 'sort_order' => 63],

            // Customer Directory (read-only for super admin)
            ['module' => 'customers_directory', 'name' => 'View Customer Directory', 'slug' => 'customers_directory.view', 'sort_order' => 70],

            ['module' => 'settings', 'name' => 'View Settings', 'slug' => 'settings.view', 'sort_order' => 80],
            ['module' => 'settings', 'name' => 'Update Settings', 'slug' => 'settings.update', 'sort_order' => 81],
        ];

        $permissionIds = [];
        foreach ($permissions as $p) {
            $perm = Permission::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'module' => $p['module'],
                    'name' => $p['name'],
                    'scope' => RoleScope::SUPER_ADMIN->value,
                    'sort_order' => $p['sort_order'],
                    'status' => 'active',
                ]
            );
            $permissionIds[] = $perm->id;
        }

        // 2. Create Platform Owner system role
        $role = Role::updateOrCreate(
            ['slug' => 'platform-owner', 'scope' => RoleScope::SUPER_ADMIN->value],
            [
                'name' => 'Platform Owner',
                'description' => 'Full administrative control over the SaaS platform',
                'is_system' => true,
                'status' => 'active',
                'organization_id' => null,
            ]
        );

        $role->permissions()->sync($permissionIds);

        // 3. Seed default Super Admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@kanaksaas.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('Password123!'),
                'status' => 'active',
            ]
        );

        UserRole::updateOrCreate(
            ['user_id' => $admin->id, 'role_id' => $role->id],
            ['organization_id' => null]
        );
    }
}
