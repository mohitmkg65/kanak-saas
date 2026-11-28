<?php

use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationStatus;
use App\Enums\OrganizationSubscriptionStatus;
use App\Enums\RegistrationSource;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(SuperAdminSeeder::class);
    $this->seed(OrganizationSeeder::class);

    $this->admin = User::where('email', 'admin@kanaksaas.com')->first();
});

test('public self-registration sets origin source to self_registered and logs initial status', function () {
    $response = $this->post('/register', [
        'organization_name' => 'Self Registered Steel Mills',
        'owner_name' => 'Rajesh Sharma',
        'email' => 'rajesh@steelmills.com',
        'phone' => '+91 99988 77766',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => '1',
    ]);

    $response->assertRedirect(route('login'));

    $org = Organization::where('email', 'rajesh@steelmills.com')->first();
    expect($org)->not->toBeNull();
    expect($org->registration_source)->toBe(RegistrationSource::SelfRegistered);
    expect($org->approval_status)->toBe(OrganizationApprovalStatus::Pending);
    expect($org->status)->toBe(OrganizationStatus::INACTIVE);
    expect($org->subscription_status)->toBe(OrganizationSubscriptionStatus::Trialing);

    // Verify status log was written
    $log = $org->statusLogs()->first();
    expect($log)->not->toBeNull();
    expect($log->action)->toBe('registered');
    expect($log->to_status)->toBe('inactive');
    expect($log->to_approval_status)->toBe('pending');
});

test('super admin creating organization sets origin source to admin_created and logs creation', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.organizations.store'), [
        'name' => 'Admin Created Ceramics',
        'slug' => 'admin-created-ceramics',
        'email' => 'contact@ceramics.com',
        'phone' => '+91 91234 56789',
        'status' => 'active',
        'currency' => 'INR',
        'timezone' => 'Asia/Kolkata',
        'locale' => 'en',
    ]);

    $response->assertRedirect(route('admin.organizations.index'));

    $org = Organization::where('slug', 'admin-created-ceramics')->first();
    expect($org)->not->toBeNull();
    expect($org->registration_source)->toBe(RegistrationSource::AdminCreated);
    expect($org->approval_status)->toBe(OrganizationApprovalStatus::Approved);
    expect($org->status)->toBe(OrganizationStatus::ACTIVE);
    expect($org->created_by)->toBe($this->admin->id);

    $log = $org->statusLogs()->first();
    expect($log)->not->toBeNull();
    expect($log->action)->toBe('registered');
    expect($log->action_by)->toBe($this->admin->id);
});

test('super admin can mark pending organization under review and then approve it', function () {
    $org = Organization::factory()->pending()->create();

    // Mark under review
    $reviewResponse = $this->actingAs($this->admin)->post(route('admin.organizations.review', $org));
    $reviewResponse->assertRedirect(route('admin.organizations.show', $org));

    $org->refresh();
    expect($org->approval_status)->toBe(OrganizationApprovalStatus::UnderReview);

    // Approve
    $approveResponse = $this->actingAs($this->admin)->post(route('admin.organizations.approve', $org), [
        'remarks' => 'Verification complete, business approved',
    ]);
    $approveResponse->assertRedirect(route('admin.organizations.show', $org));

    $org->refresh();
    expect($org->approval_status)->toBe(OrganizationApprovalStatus::Approved);
    expect($org->status)->toBe(OrganizationStatus::ACTIVE);

    // Check status logs
    $logs = $org->statusLogs()->get();
    expect($logs->pluck('action')->all())->toContain('under_review', 'approved');
});

test('super admin can reject organization with reason, and re-approve it with remarks', function () {
    $org = Organization::factory()->pending()->create();

    // Reject organization
    $rejectResponse = $this->actingAs($this->admin)->post(route('admin.organizations.reject', $org), [
        'rejection_reason' => 'GST certificate expired and duplicate email used.',
    ]);
    $rejectResponse->assertRedirect(route('admin.organizations.show', $org));

    $org->refresh();
    expect($org->approval_status)->toBe(OrganizationApprovalStatus::Rejected);
    expect($org->status)->toBe(OrganizationStatus::INACTIVE);
    expect($org->rejection_reason)->toBe('GST certificate expired and duplicate email used.');

    $rejectLog = $org->statusLogs()->where('action', 'rejected')->first();
    expect($rejectLog)->not->toBeNull();
    expect($rejectLog->reason)->toBe('GST certificate expired and duplicate email used.');
    expect($rejectLog->action_by)->toBe($this->admin->id);

    // Re-Approve rejected organization
    $reapproveResponse = $this->actingAs($this->admin)->post(route('admin.organizations.reapprove', $org), [
        'remarks' => 'Corrected GST certificate verified by compliance officer.',
    ]);
    $reapproveResponse->assertRedirect(route('admin.organizations.show', $org));

    $org->refresh();
    expect($org->approval_status)->toBe(OrganizationApprovalStatus::Approved);
    expect($org->status)->toBe(OrganizationStatus::ACTIVE);
    expect($org->rejection_reason)->toBeNull();

    $reapproveLog = $org->statusLogs()->where('action', 'reapproved')->first();
    expect($reapproveLog)->not->toBeNull();
    expect($reapproveLog->from_approval_status)->toBe('rejected');
    expect($reapproveLog->to_approval_status)->toBe('approved');
    expect($reapproveLog->reason)->toBe('Corrected GST certificate verified by compliance officer.');
    expect($reapproveLog->action_by)->toBe($this->admin->id);
});

test('super admin can suspend organization and reactivate it with separate logs', function () {
    $org = Organization::factory()->create([
        'status' => OrganizationStatus::ACTIVE->value,
        'approval_status' => OrganizationApprovalStatus::Approved->value,
    ]);

    // Suspend organization
    $suspendResponse = $this->actingAs($this->admin)->post(route('admin.organizations.suspend', $org), [
        'suspension_reason' => 'Subscription payment failed for 3 consecutive billing attempts.',
    ]);
    $suspendResponse->assertRedirect(route('admin.organizations.show', $org));

    $org->refresh();
    expect($org->status)->toBe(OrganizationStatus::SUSPENDED);
    // approval_status remains approved
    expect($org->approval_status)->toBe(OrganizationApprovalStatus::Approved);

    $suspendLog = $org->statusLogs()->where('action', 'suspended')->first();
    expect($suspendLog)->not->toBeNull();
    expect($suspendLog->to_status)->toBe('suspended');
    expect($suspendLog->reason)->toBe('Subscription payment failed for 3 consecutive billing attempts.');

    // Reactivate organization
    $reactivateResponse = $this->actingAs($this->admin)->post(route('admin.organizations.reactivate', $org), [
        'remarks' => 'Invoice payment received and cleared.',
    ]);
    $reactivateResponse->assertRedirect(route('admin.organizations.show', $org));

    $org->refresh();
    expect($org->status)->toBe(OrganizationStatus::ACTIVE);

    $reactivateLog = $org->statusLogs()->where('action', 'reactivated')->first();
    expect($reactivateLog)->not->toBeNull();
    expect($reactivateLog->from_status)->toBe('suspended');
    expect($reactivateLog->to_status)->toBe('active');
    expect($reactivateLog->reason)->toBe('Invoice payment received and cleared.');
});

test('organization show page displays all 3 statuses, origin source, and history audit trail', function () {
    $org = Organization::factory()->create([
        'name' => 'Timber Craft Ltd',
        'status' => OrganizationStatus::ACTIVE->value,
        'approval_status' => OrganizationApprovalStatus::Approved->value,
        'subscription_status' => OrganizationSubscriptionStatus::Trialing->value,
        'registration_source' => RegistrationSource::AdminCreated->value,
    ]);

    $org->statusLogs()->create([
        'action' => 'registered',
        'from_status' => null,
        'to_status' => 'active',
        'from_approval_status' => null,
        'to_approval_status' => 'approved',
        'reason' => 'Initial setup by admin',
        'action_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.organizations.show', $org));
    $response->assertOk();
    $response->assertSee('Timber Craft Ltd');
    $response->assertSee('Approval State');
    $response->assertSee('Operational Status');
    $response->assertSee('Subscription');
    $response->assertSee('Origin / Source');
    $response->assertSee('Admin Created');
    $response->assertSee('Lifecycle Audit Trail');
    $response->assertSee('Initial setup by admin');
});
