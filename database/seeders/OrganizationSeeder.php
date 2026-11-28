<?php

namespace Database\Seeders;

use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationSubscriptionStatus;
use App\Enums\RegistrationSource;
use App\Enums\RoleScope;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed ORGANIZATION permissions
        $permissions = [
            ['module' => 'dashboard', 'name' => 'View Dashboard', 'slug' => 'dashboard.view', 'sort_order' => 10],

            ['module' => 'customers', 'name' => 'View Customers', 'slug' => 'customers.view', 'sort_order' => 15],
            ['module' => 'customers', 'name' => 'Create Customer', 'slug' => 'customers.create', 'sort_order' => 16],
            ['module' => 'customers', 'name' => 'Update Customer', 'slug' => 'customers.update', 'sort_order' => 17],
            ['module' => 'customers', 'name' => 'Delete Customer', 'slug' => 'customers.delete', 'sort_order' => 18],

            ['module' => 'products', 'name' => 'View Products', 'slug' => 'products.view', 'sort_order' => 19],
            ['module' => 'products', 'name' => 'Add Product', 'slug' => 'products.create', 'sort_order' => 20],
            ['module' => 'products', 'name' => 'Update Product', 'slug' => 'products.update', 'sort_order' => 21],
            ['module' => 'products', 'name' => 'Delete Product', 'slug' => 'products.delete', 'sort_order' => 22],
            ['module' => 'products', 'name' => 'Browse Catalog', 'slug' => 'products.catalog', 'sort_order' => 23],

            ['module' => 'users', 'name' => 'View System Users', 'slug' => 'users.view', 'sort_order' => 30],
            ['module' => 'users', 'name' => 'Create System User', 'slug' => 'users.create', 'sort_order' => 31],
            ['module' => 'users', 'name' => 'Update System User', 'slug' => 'users.update', 'sort_order' => 32],
            ['module' => 'users', 'name' => 'Delete System User', 'slug' => 'users.delete', 'sort_order' => 33],

            ['module' => 'roles', 'name' => 'View Roles', 'slug' => 'roles.view', 'sort_order' => 40],
            ['module' => 'roles', 'name' => 'Create Role', 'slug' => 'roles.create', 'sort_order' => 41],
            ['module' => 'roles', 'name' => 'Update Role', 'slug' => 'roles.update', 'sort_order' => 42],
            ['module' => 'roles', 'name' => 'Delete Role', 'slug' => 'roles.delete', 'sort_order' => 43],

            ['module' => 'settings', 'name' => 'View Settings', 'slug' => 'settings.view', 'sort_order' => 50],
            ['module' => 'settings', 'name' => 'Update Settings', 'slug' => 'settings.update', 'sort_order' => 51],

            ['module' => 'profile', 'name' => 'View Profile', 'slug' => 'profile.view', 'sort_order' => 60],
            ['module' => 'profile', 'name' => 'Update Profile', 'slug' => 'profile.update', 'sort_order' => 61],
        ];

        $permissionIds = [];
        foreach ($permissions as $p) {
            $perm = Permission::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'module' => $p['module'],
                    'name' => $p['name'],
                    'scope' => RoleScope::ORGANIZATION->value,
                    'sort_order' => $p['sort_order'],
                    'status' => 'active',
                ]
            );
            $permissionIds[] = $perm->id;
        }

        // 2. Create Sample Organization 1: Kanak Building Supplies
        $org1 = Organization::updateOrCreate(
            ['slug' => 'kanak-building-supplies'],
            [
                'name' => 'Kanak Building Supplies',
                'legal_name' => 'Kanak Building Supplies Pvt Ltd',
                'email' => 'contact@kanaksupplies.com',
                'phone' => '+91 98765 43210',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'status' => 'active',
                'approval_status' => OrganizationApprovalStatus::Approved->value,
                'subscription_status' => OrganizationSubscriptionStatus::Active->value,
                'registration_source' => RegistrationSource::AdminCreated->value,
                'approved_at' => now(),
                'currency' => 'INR',
                'timezone' => 'Asia/Kolkata',
                'locale' => 'en',
            ]
        );

        $role1 = Role::updateOrCreate(
            ['slug' => 'organization-owner', 'organization_id' => $org1->id],
            [
                'name' => 'Organization Owner',
                'scope' => RoleScope::ORGANIZATION->value,
                'description' => 'Full control over Kanak Building Supplies',
                'is_system' => true,
                'status' => 'active',
            ]
        );
        $role1->permissions()->sync($permissionIds);

        $owner1 = User::updateOrCreate(
            ['email' => 'owner@kanaksupplies.com'],
            [
                'name' => 'Kanak Owner',
                'password' => Hash::make('Password123!'),
                'status' => 'active',
            ]
        );

        OrganizationMembership::updateOrCreate(
            ['organization_id' => $org1->id, 'user_id' => $owner1->id],
            ['status' => 'active', 'joined_at' => now()]
        );

        UserRole::updateOrCreate(
            ['user_id' => $owner1->id, 'role_id' => $role1->id, 'organization_id' => $org1->id]
        );

        // 3. Create Sample Organization 2: Apex Materials
        $org2 = Organization::updateOrCreate(
            ['slug' => 'apex-materials'],
            [
                'name' => 'Apex Materials',
                'legal_name' => 'Apex Materials Corp',
                'email' => 'contact@apexmaterials.com',
                'phone' => '+91 87654 32109',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'country' => 'India',
                'status' => 'active',
                'approval_status' => OrganizationApprovalStatus::Approved->value,
                'subscription_status' => OrganizationSubscriptionStatus::Active->value,
                'registration_source' => RegistrationSource::AdminCreated->value,
                'approved_at' => now(),
                'currency' => 'INR',
                'timezone' => 'Asia/Kolkata',
                'locale' => 'en',
            ]
        );

        $role2 = Role::updateOrCreate(
            ['slug' => 'organization-owner', 'organization_id' => $org2->id],
            [
                'name' => 'Organization Owner',
                'scope' => RoleScope::ORGANIZATION->value,
                'description' => 'Full control over Apex Materials',
                'is_system' => true,
                'status' => 'active',
            ]
        );
        $role2->permissions()->sync($permissionIds);

        $owner2 = User::updateOrCreate(
            ['email' => 'owner@apexmaterials.com'],
            [
                'name' => 'Apex Owner',
                'password' => Hash::make('Password123!'),
                'status' => 'active',
            ]
        );

        OrganizationMembership::updateOrCreate(
            ['organization_id' => $org2->id, 'user_id' => $owner2->id],
            ['status' => 'active', 'joined_at' => now()]
        );

        UserRole::updateOrCreate(
            ['user_id' => $owner2->id, 'role_id' => $role2->id, 'organization_id' => $org2->id]
        );
    }
}
