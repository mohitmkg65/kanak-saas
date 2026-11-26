<?php

use App\Enums\RoleScope;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\OrganizationContextService;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\SuperAdminSeeder;

beforeEach(function () {
    $this->seed(SuperAdminSeeder::class);
    $this->seed(OrganizationSeeder::class);
});

test('super admin can create super admin role with permission matrix', function () {
    $admin = User::where('email', 'admin@kanaksaas.com')->first();
    $this->actingAs($admin);

    $perm = Permission::where('scope', RoleScope::SUPER_ADMIN->value)->first();

    $response = $this->post('/admin/roles', [
        'name' => 'Audit Specialist',
        'description' => 'Platform auditor role',
        'status' => 'active',
        'permissions' => [$perm->id],
    ]);

    $response->assertRedirect('/admin/roles');
    $this->assertDatabaseHas('roles', [
        'name' => 'Audit Specialist',
        'scope' => RoleScope::SUPER_ADMIN->value,
        'organization_id' => null,
    ]);
});

test('organization user can create organization role with permission matrix', function () {
    $user = User::where('email', 'owner@kanaksupplies.com')->first();
    $org = Organization::where('slug', 'kanak-building-supplies')->first();

    app(OrganizationContextService::class)->set($org, $user);
    $this->actingAs($user);

    $perm = Permission::where('scope', RoleScope::ORGANIZATION->value)->first();

    $response = $this->post('/organization/roles', [
        'name' => 'Sales Supervisor',
        'description' => 'Sales lead role',
        'status' => 'active',
        'permissions' => [$perm->id],
    ]);

    $response->assertRedirect('/organization/roles');
    $this->assertDatabaseHas('roles', [
        'name' => 'Sales Supervisor',
        'scope' => RoleScope::ORGANIZATION->value,
        'organization_id' => $org->id,
    ]);
});

test('system role cannot be deleted', function () {
    $admin = User::where('email', 'admin@kanaksaas.com')->first();
    $this->actingAs($admin);

    $systemRole = Role::where('slug', 'platform-owner')->first();

    $response = $this->delete("/admin/roles/{$systemRole->public_id}");
    $response->assertSessionHasErrors();

    $this->assertDatabaseHas('roles', ['id' => $systemRole->id]);
});

test('assigned role cannot be deleted without reassigning users', function () {
    $user = User::where('email', 'owner@kanaksupplies.com')->first();
    $org = Organization::where('slug', 'kanak-building-supplies')->first();

    app(OrganizationContextService::class)->set($org, $user);
    $this->actingAs($user);

    $assignedRole = Role::where('slug', 'organization-owner')->first();

    $response = $this->delete("/organization/roles/{$assignedRole->public_id}");
    $response->assertSessionHasErrors();

    $this->assertDatabaseHas('roles', ['id' => $assignedRole->id]);
});
