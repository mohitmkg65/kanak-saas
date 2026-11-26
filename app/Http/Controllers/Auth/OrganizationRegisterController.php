<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OrganizationApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterOrganizationRequest;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationRegisterController extends Controller
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterOrganizationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            // Create the owner user
            $user = User::create([
                'name' => $data['owner_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => 'active',
            ]);

            // Create the organization in pending state
            $orgName = $data['organization_name'];
            $organization = Organization::create([
                'name' => $orgName,
                'slug' => Str::slug($orgName).'-'.Str::lower(Str::random(6)),
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? 'India',
                'status' => 'inactive',
                'approval_status' => OrganizationApprovalStatus::Pending->value,
                'owner_email' => $data['email'],
                'timezone' => 'Asia/Kolkata',
                'currency' => 'INR',
                'locale' => 'en',
            ]);

            // Create membership
            OrganizationMembership::create([
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'status' => 'active',
                'joined_at' => now(),
            ]);

            $this->auditLog->log(
                action: 'organization.registered',
                module: 'organizations',
                entityType: Organization::class,
                entityId: $organization->id,
                newValues: ['name' => $organization->name, 'owner_email' => $user->email]
            );
        });

        return redirect()->route('login')->with('success',
            'Your registration has been submitted successfully! Our team will review your application and get back to you within 24–48 hours.'
        );
    }
}
