<?php

namespace App\Services;

use App\Enums\RoleScope;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRoleService
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    public function createSuperAdminUser(array $data, int $roleId): User
    {
        return DB::transaction(function () use ($data, $roleId) {
            $user = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => $data['status'] ?? 'active',
            ]);

            $role = Role::where('id', $roleId)
                ->where('scope', RoleScope::SUPER_ADMIN->value)
                ->where('status', 'active')
                ->firstOrFail();

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'organization_id' => null,
            ]);

            $this->auditLog->log(
                action: 'user.created',
                module: 'users',
                entityType: User::class,
                entityId: $user->id,
                newValues: ['name' => $user->name, 'email' => $user->email, 'scope' => 'SUPER_ADMIN']
            );

            return $user;
        });
    }

    public function createOrganizationUser(array $data, int $roleId, Organization $organization): User
    {
        return DB::transaction(function () use ($data, $roleId, $organization) {
            $user = User::create([
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'status' => $data['status'] ?? 'active',
            ]);

            OrganizationMembership::create([
                'organization_id' => $organization->id,
                'user_id' => $user->id,
                'status' => 'active',
                'joined_at' => now(),
            ]);

            $role = Role::where('id', $roleId)
                ->where('scope', RoleScope::ORGANIZATION->value)
                ->where('organization_id', $organization->id)
                ->where('status', 'active')
                ->firstOrFail();

            UserRole::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'organization_id' => $organization->id,
            ]);

            $this->auditLog->log(
                action: 'user.created',
                module: 'users',
                entityType: User::class,
                entityId: $user->id,
                newValues: ['name' => $user->name, 'email' => $user->email, 'scope' => 'ORGANIZATION'],
                organizationId: $organization->id
            );

            return $user;
        });
    }

    public function updateUser(User $user, array $data, ?int $roleId = null, ?Organization $organization = null): User
    {
        return DB::transaction(function () use ($user, $data, $roleId, $organization) {
            $oldValues = ['name' => $user->name, 'status' => $user->status];

            $updateData = [
                'name' => $data['name'],
                'email' => strtolower($data['email']),
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'] ?? $user->status,
            ];

            if (! empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            if ($roleId) {
                if ($organization) {
                    $role = Role::where('id', $roleId)
                        ->where('scope', RoleScope::ORGANIZATION->value)
                        ->where('organization_id', $organization->id)
                        ->where('status', 'active')
                        ->firstOrFail();

                    UserRole::updateOrCreate(
                        ['user_id' => $user->id, 'organization_id' => $organization->id],
                        ['role_id' => $role->id]
                    );
                } else {
                    $role = Role::where('id', $roleId)
                        ->where('scope', RoleScope::SUPER_ADMIN->value)
                        ->where('status', 'active')
                        ->firstOrFail();

                    UserRole::updateOrCreate(
                        ['user_id' => $user->id, 'organization_id' => null],
                        ['role_id' => $role->id]
                    );
                }
            }

            $this->auditLog->log(
                action: 'user.updated',
                module: 'users',
                entityType: User::class,
                entityId: $user->id,
                oldValues: $oldValues,
                newValues: ['name' => $user->name, 'status' => $user->status],
                organizationId: $organization?->id
            );

            return $user;
        });
    }
}
