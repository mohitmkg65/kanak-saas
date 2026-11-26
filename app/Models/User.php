<?php

namespace App\Models;

use App\Enums\RoleScope;
use App\Enums\UserStatus;
use App\Traits\HasPublicId;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasPublicId, Notifiable;

    protected $fillable = [
        'public_id',
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
        ];
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function activeMemberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class)->where('status', 'active');
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->userRoles()
            ->whereHas('role', function ($query) {
                $query->where('scope', RoleScope::SUPER_ADMIN->value)
                    ->where('status', 'active');
            })
            ->exists();
    }

    public function isMemberOf(int $organizationId): bool
    {
        return $this->memberships()
            ->where('organization_id', $organizationId)
            ->where('status', 'active')
            ->exists();
    }

    public function hasPermission(string $permissionSlug, ?int $organizationId = null): bool
    {
        if ($this->isSuperAdmin()) {
            $hasSuperAdminPermission = $this->userRoles()
                ->whereNull('organization_id')
                ->whereHas('role', function ($q) use ($permissionSlug) {
                    $q->where('scope', RoleScope::SUPER_ADMIN->value)
                        ->where('status', 'active')
                        ->whereHas('permissions', function ($pq) use ($permissionSlug) {
                            $pq->where('slug', $permissionSlug)->where('status', 'active');
                        });
                })->exists();

            if ($hasSuperAdminPermission) {
                return true;
            }
        }

        if ($organizationId) {
            return $this->userRoles()
                ->where('organization_id', $organizationId)
                ->whereHas('role', function ($q) use ($permissionSlug) {
                    $q->where('scope', RoleScope::ORGANIZATION->value)
                        ->where('status', 'active')
                        ->whereHas('permissions', function ($pq) use ($permissionSlug) {
                            $pq->where('slug', $permissionSlug)->where('status', 'active');
                        });
                })->exists();
        }

        return false;
    }
}
