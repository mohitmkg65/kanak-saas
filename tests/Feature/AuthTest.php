<?php

use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(SuperAdminSeeder::class);
    $this->seed(OrganizationSeeder::class);
});

test('login page renders successfully', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
    $response->assertSee('Sign in to your account');
});

test('super admin login redirects to admin dashboard', function () {
    $response = $this->post('/login', [
        'email' => 'admin@kanaksaas.com',
        'password' => 'Password123!',
    ]);

    $response->assertRedirect('/admin/dashboard');
    $this->assertAuthenticated();
});

test('single organization user login redirects to organization dashboard', function () {
    $response = $this->post('/login', [
        'email' => 'owner@kanaksupplies.com',
        'password' => 'Password123!',
    ]);

    $response->assertRedirect('/organization/dashboard');
    $this->assertAuthenticated();
});

test('multi organization user login redirects to organization selection screen', function () {
    $user = User::where('email', 'owner@kanaksupplies.com')->first();
    $org2 = Organization::where('slug', 'apex-materials')->first();

    OrganizationMembership::create([
        'organization_id' => $org2->id,
        'user_id' => $user->id,
        'status' => 'active',
    ]);

    $response = $this->post('/login', [
        'email' => 'owner@kanaksupplies.com',
        'password' => 'Password123!',
    ]);

    $response->assertRedirect('/select-organization');
    $this->assertAuthenticated();
});

test('invalid credentials returns validation error', function () {
    $response = $this->post('/login', [
        'email' => 'admin@kanaksaas.com',
        'password' => 'WrongPassword!',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('inactive or suspended user cannot log in', function () {
    $user = User::where('email', 'owner@kanaksupplies.com')->first();
    $user->update(['status' => 'suspended']);

    $response = $this->post('/login', [
        'email' => 'owner@kanaksupplies.com',
        'password' => 'Password123!',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('user can log out securely via POST', function () {
    $user = User::where('email', 'admin@kanaksaas.com')->first();
    $this->actingAs($user);

    $response = $this->post('/logout');
    $response->assertRedirect('/login');
    $this->assertGuest();
});

test('user can change password with current password verification', function () {
    $user = User::where('email', 'admin@kanaksaas.com')->first();
    $this->actingAs($user);

    $response = $this->put('/admin/profile/password', [
        'current_password' => 'Password123!',
        'password' => 'NewSecurePass123!',
        'password_confirmation' => 'NewSecurePass123!',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertTrue(Hash::check('NewSecurePass123!', $user->fresh()->password));
});
