# RBAC (Role-Based Access Control) Specification

## Schema Design

- `roles`: Centralized table with `scope` (`SUPER_ADMIN` vs `ORGANIZATION`) and `organization_id` (nullable).
- `permissions`: Grouped by `module` and `scope`.
- `role_permissions`: Pivot table linking roles to permissions.
- `user_roles`: Pivot table linking users to roles, bound to an optional `organization_id`.

## Rules & Safety

1. **System Roles** (`is_system = true`): Cannot be hard deleted.
2. **Assigned Roles**: Cannot be deleted while actively assigned to users.
3. **Scope Integrity**: Super Admin forms display `SUPER_ADMIN` permissions only. Organization forms display `ORGANIZATION` permissions for the active organization only.
