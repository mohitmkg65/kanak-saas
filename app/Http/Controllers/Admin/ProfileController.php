<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    public function show(): View
    {
        $user = Auth::user();

        return view('admin.profile.index', compact('user'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $user->update($request->validated());

        $this->auditLog->log(
            action: 'profile.updated',
            module: 'profile',
            entityType: get_class($user),
            entityId: $user->id
        );

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        $this->auditLog->log(
            action: 'profile.password_changed',
            module: 'profile',
            entityType: get_class($user),
            entityId: $user->id
        );

        return back()->with('success', 'Password updated successfully.');
    }
}
