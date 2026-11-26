# SaaS Architecture Documentation

## Core Architectural Design

The Building Material Shop SaaS application follows a traditional server-rendered Laravel Blade architecture enhanced with jQuery for interactive micro-interactions, styled using Tailwind CSS.

### Top-Level Security Contexts

```
SaaS PLATFORM
│
├── SUPER ADMIN CONTEXT (/admin/*)
│   ├── Super Admin Dynamic Roles (scope = SUPER_ADMIN, organization_id = NULL)
│   ├── Platform System Users
│   ├── Organization Management
│   ├── Platform Settings
│   └── System Audit Logs
│
└── ORGANIZATION CONTEXT (/organization/*)
    ├── Active Organization Context (session-based)
    ├── Organization Dynamic Roles (scope = ORGANIZATION, organization_id = active org)
    ├── Organization System Users
    ├── Organization Settings
    └── Future Business Modules (Products, Inventory, Sales, Purchases, Deliveries)
```

### Directory & Component Organization

- `app/Enums/`: Enumerations (`RoleScope`, `UserStatus`, `OrganizationStatus`).
- `app/Services/`: Core business & security services (`OrganizationContextService`, `AuthorizationService`, `RoleService`, `UserRoleService`, `SettingService`, `AuditLogService`).
- `app/Http/Middleware/`: Security context middleware (`EnsureSuperAdminContext`, `EnsureOrganizationContext`, `EnsureOrganizationMembership`).
- `app/Http/Controllers/`: Scoped controllers (`Auth\*`, `Admin\*`, `Organization\*`).
- `resources/views/layouts/`: Blade layouts (`auth.blade.php`, `admin.blade.php`, `organization.blade.php`).
