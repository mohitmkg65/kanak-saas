<?php

namespace App\Services;

use App\Enums\RoleScope;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleService
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    public function createRole(array $data, RoleScope $scope, ?int $organizationId = null, ?User $creator = null): Role
    {
        return DB::transaction(function () use ($data, $scope, $organizationId, $creator) {
            $role = Role::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'scope' => $scope->value,
                'organization_id' => $scope === RoleScope::SUPER_ADMIN ? null : $organizationId,
                'description' => $data['description'] ?? null,
                'is_system' => false,
                'status' => $data['status'] ?? 'active',
                'created_by' => $creator?->id,
            ]);

            if (! empty($data['permissions'])) {
                // Ensure permission ids belong to the same scope
                $validPermissionIds = Permission::whereIn('id', $data['permissions'])
                    ->where('scope', $scope->value)
                    ->where('status', 'active')
                    ->pluck('id');

                $role->permissions()->sync($validPermissionIds);
            }

            $this->auditLog->log(
                action: 'role.created',
                module: 'roles',
                entityType: Role::class,
                entityId: $role->id,
                newValues: ['name' => $role->name, 'scope' => $role->scope->value],
                organizationId: $role->organization_id
            );

            return $role;
        });
    }

    public function updateRole(Role $role, array $data, RoleScope $scope): Role
    {
        return DB::transaction(function () use ($role, $data, $scope) {
            $oldValues = ['name' => $role->name, 'status' => $role->status];

            $role->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? $role->status,
            ]);

            if (isset($data['permissions'])) {
                $validPermissionIds = Permission::whereIn('id', $data['permissions'])
                    ->where('scope', $scope->value)
                    ->where('status', 'active')
                    ->pluck('id');

                $role->permissions()->sync($validPermissionIds);
            }

            $this->auditLog->log(
                action: 'role.updated',
                module: 'roles',
                entityType: Role::class,
                entityId: $role->id,
                oldValues: $oldValues,
                newValues: ['name' => $role->name, 'status' => $role->status],
                organizationId: $role->organization_id
            );

            return $role;
        });
    }

    public function deleteRole(Role $role): array
    {
        if ($role->is_system) {
            return [
                'success' => false,
                'message' => 'System roles cannot be deleted.',
            ];
        }

        if ($role->userRoles()->exists()) {
            return [
                'success' => false,
                'message' => 'Role is currently assigned to users. Reassign users before deletion or deactivate the role.',
            ];
        }

        DB::transaction(function () use ($role) {
            $this->auditLog->log(
                action: 'role.deleted',
                module: 'roles',
                entityType: Role::class,
                entityId: $role->id,
                oldValues: ['name' => $role->name],
                organizationId: $role->organization_id
            );

            $role->permissions()->detach();
            $role->delete();
        });

        return [
            'success' => true,
            'message' => 'Role deleted successfully.',
        ];
    }
}
