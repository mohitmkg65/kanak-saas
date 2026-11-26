# Multi-Tenancy & Tenant Isolation

## Server-Side Organization Context

Tenant isolation is strictly maintained **server-side**. The frontend NEVER passes `organization_id` in request inputs to select target records.

### Guarantees

1. `OrganizationContextService`: Maintains `active_organization_id` in session.
2. All organization-scoped queries apply `where('organization_id', $currentOrgId)` automatically via controller and service boundaries.
3. Cross-Tenant Protection: Organization A users cannot query, view, edit, or assign Organization B's users, roles, or settings.
4. Duplicate Name Isolation: Roles with identical names (e.g. "Inventory Manager") in different organizations remain strictly isolated by their `organization_id` foreign key.
