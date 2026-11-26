<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class OrganizationContextService
{
    public const SESSION_KEY = 'active_organization_id';

    /**
     * Get the currently active organization for the authenticated user session.
     */
    public function current(): ?Organization
    {
        $orgId = Session::get(self::SESSION_KEY);

        if (! $orgId) {
            return null;
        }

        return Organization::where('id', $orgId)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Get the current active organization ID.
     */
    public function id(): ?int
    {
        return Session::get(self::SESSION_KEY);
    }

    /**
     * Set the active organization context for the given user.
     */
    public function set(Organization $organization, User $user): bool
    {
        if (! $user->isMemberOf($organization->id) && ! $user->isSuperAdmin()) {
            return false;
        }

        if ($organization->status->value !== 'active') {
            return false;
        }

        Session::put(self::SESSION_KEY, $organization->id);

        return true;
    }

    /**
     * Clear the current active organization context.
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Check if active organization is established.
     */
    public function hasContext(): bool
    {
        $current = $this->current();

        return $current !== null;
    }
}
