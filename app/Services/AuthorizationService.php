<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthorizationService
{
    public function __construct(
        protected OrganizationContextService $context
    ) {}

    public function can(string $permissionSlug, ?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (! $user || $user->status->value !== 'active') {
            return false;
        }

        $orgId = $this->context->id();

        return $user->hasPermission($permissionSlug, $orgId);
    }
}
