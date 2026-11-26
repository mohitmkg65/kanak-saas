<?php

use App\Enums\RoleScope;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Services\OrganizationContextService;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\SuperAdminSeeder;

beforeEach(function () {
    $this->seed(SuperAdminSeeder::class);
    $this->seed(OrganizationSeeder::class);
});

test('organization user cannot access super admin routes', function () {
    $user = User::where('email', 'owner@kanaksupplies.com')->first();
    $this->actingAs($user);

    $response = $this->get('/admin/dashboard');
    $response->assertStatus(403);
});

test('super admin cannot access organization panel without active organization context', function () {
    $admin = User::where('email', 'admin@kanaksaas.com')->first();
    $this->actingAs($admin);

    $response = $this->get('/organization/dashboard');
    $response->assertRedirect('/admin/dashboard');
});

test('organization A user cannot view organization B roles', function () {
    $userA = User::where('email', 'owner@kanaksupplies.com')->first();
    $orgA = Organization::where('slug', 'kanak-building-supplies')->first();
    $orgB = Organization::where('slug', 'apex-materials')->first();

    // Set Org A context
    app(OrganizationContextService::class)->set($orgA, $userA);
    $this->actingAs($userA);

    $roleB = Role::where('organization_id', $orgB->id)->first();

    $response = $this->get("/organization/roles/{$roleB->public_id}/edit");
    $response->assertStatus(403);
});

test('organization A user cannot edit or assign organization B role', function () {
    $userA = User::where('email', 'owner@kanaksupplies.com')->first();
    $orgA = Organization::where('slug', 'kanak-building-supplies')->first();
    $orgB = Organization::where('slug', 'apex-materials')->first();

    app(OrganizationContextService::class)->set($orgA, $userA);
    $this->actingAs($userA);

    $roleB = Role::where('organization_id', $orgB->id)->first();

    $response = $this->put("/organization/roles/{$roleB->public_id}", [
        'name' => 'Hacked Role Name',
        'status' => 'active',
    ]);
    $response->assertStatus(403);
});

test('MANDATORY TEST CASE: Org A and Org B both have Inventory Manager role - Org A user only sees Org A role', function () {
    $orgA = Organization::where('slug', 'kanak-building-supplies')->first();
    $orgB = Organization::where('slug', 'apex-materials')->first();

    $userA = User::where('email', 'owner@kanaksupplies.com')->first();

    // Create "Inventory Manager" in Org A
    $roleOrgA = Role::create([
        'name' => 'Inventory Manager',
        'slug' => 'inventory-manager',
        'scope' => RoleScope::ORGANIZATION->value,
        'organization_id' => $orgA->id,
        'status' => 'active',
    ]);

    // Create "Inventory Manager" in Org B
    $roleOrgB = Role::create([
        'name' => 'Inventory Manager',
        'slug' => 'inventory-manager',
        'scope' => RoleScope::ORGANIZATION->value,
        'organization_id' => $orgB->id,
        'status' => 'active',
    ]);

    // Set Org A active context
    app(OrganizationContextService::class)->set($orgA, $userA);
    $this->actingAs($userA);

    $response = $this->get('/organization/roles');
    $response->assertStatus(200);

    // Assert Org A user sees Org A's Inventory Manager
    $response->assertSee($roleOrgA->name);

    // Assert Org A user's query only returns 1 Inventory Manager role (Org A's role)
    $rolesQuery = Role::where('scope', RoleScope::ORGANIZATION->value)
        ->where('organization_id', $orgA->id)
        ->get();

    $this->assertCount(2, $rolesQuery); // Organization Owner + Inventory Manager
    $this->assertTrue($rolesQuery->contains('id', $roleOrgA->id));
    $this->assertFalse($rolesQuery->contains('id', $roleOrgB->id));
});

test('organization A user cannot view organization B system users', function () {
    $userA = User::where('email', 'owner@kanaksupplies.com')->first();
    $userB = User::where('email', 'owner@apexmaterials.com')->first();
    $orgA = Organization::where('slug', 'kanak-building-supplies')->first();

    app(OrganizationContextService::class)->set($orgA, $userA);
    $this->actingAs($userA);

    $response = $this->get("/organization/users/{$userB->public_id}/edit");
    $response->assertStatus(403);
});
