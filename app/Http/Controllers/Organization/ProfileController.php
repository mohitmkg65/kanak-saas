<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\AuditLogService;
use App\Services\OrganizationContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected OrganizationContextService $context,
        protected AuditLogService $auditLog
    ) {}

    public function show(): View
    {
        $user = Auth::user();
        $org = $this->context->current();

        return view('organization.profile.index', compact('user', 'org'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $org = $this->context->current();

        $user->update($request->validated());

        $this->auditLog->log(
            action: 'profile.updated',
            module: 'profile',
            entityType: get_class($user),
            entityId: $user->id,
            organizationId: $org?->id
        );

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $org = $this->context->current();

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        $this->auditLog->log(
            action: 'profile.password_changed',
            module: 'profile',
            entityType: get_class($user),
            entityId: $user->id,
            organizationId: $org?->id
        );

        return back()->with('success', 'Password updated successfully.');
    }
}
