# Authorization Documentation

## Security Context & Authorization

Authorization checks evaluate three security dimensions:

$$\text{Access} = \text{Authenticated User} + \text{Security Context} + \text{Scoped Role} + \text{Permission}$$

### Context Middleware

1. `EnsureSuperAdminContext` (`super.admin`):
   - Enforces user authenticated, active, and holds an active `SUPER_ADMIN` role.
2. `EnsureOrganizationContext` (`org.context`):
   - Enforces user authenticated, active, and has an active selected organization context in session.
3. `EnsureOrganizationMembership` (`org.membership`):
   - Enforces user holds active membership in the active organization context.
