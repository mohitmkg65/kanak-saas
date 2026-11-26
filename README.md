# Kanak SaaS - Building Material Shop SaaS Platform

Production-grade, multi-tenant SaaS application built with **Laravel 13**, **PHP 8.3**, **PostgreSQL / SQLite**, **Tailwind CSS**, and **jQuery**.

## Phase 0–2 Foundation Features

- **Single Unified Authentication**: Standardized `users` table authenticating both Super Admin platform administrators and Organization business users.
- **Top-Level Security Contexts**:
  - `/admin/*`: Super Admin Panel for SaaS platform management, organization provisioning, and platform roles/permissions.
  - `/organization/*`: Organization Business Panel for multi-tenant business operations.
- **Strict Server-Side Tenant Isolation**: Session-secured `OrganizationContextService` and security middleware (`EnsureSuperAdminContext`, `EnsureOrganizationContext`, `EnsureOrganizationMembership`). Client-supplied `organization_id` or `scope` parameters are never trusted.
- **Scoped Dynamic RBAC**: Centralized `roles` and `permissions` tables supporting `SUPER_ADMIN` and `ORGANIZATION` scopes. Includes interactive permission matrix UI with module-level and global Select All capabilities.
- **Design System**: Tailored B2B SaaS theme using **Deep Indigo (`#312E81`)** and **Electric Teal (`#0D9488`)**, fully responsive layouts, mobile drawers, and jQuery toast notifications.
- **Comprehensive Seeders & Pest Test Suite**: Seeders for platform owner & organization owner accounts. Automated test suite covering authentication, RBAC, profile security, and tenant isolation (including mandatory cross-tenant same-name role isolation tests).

---

## Installation & Setup

### Prerequisites
- PHP 8.3+
- Composer
- Node.js & npm

### Setup Steps

1. **Clone & Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Run Migrations & Seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Build Frontend Assets**
   ```bash
   npm run build
   ```

5. **Run Development Server**
   ```bash
   php artisan serve
   ```

---

## Default Seeded Credentials

| Context | Email | Password | Access URL |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@kanaksaas.com` | `Password123!` | `/admin/dashboard` |
| **Organization A Owner** | `owner@kanaksupplies.com` | `Password123!` | `/organization/dashboard` |
| **Organization B Owner** | `owner@apexmaterials.com` | `Password123!` | `/organization/dashboard` |

---

## Running Tests

Run the Pest test suite:
```bash
vendor/bin/pest
```

Format code using Pint:
```bash
vendor/bin/pint --dirty --format agent
```

---

## Documentation

Comprehensive architecture & security documentation is located in `docs/`:
- [Architecture Overview](docs/architecture.md)
- [Authentication Architecture](docs/authentication.md)
- [Authorization & RBAC](docs/authorization.md)
- [Multi-Tenancy & Tenant Isolation](docs/multi-tenancy.md)
- [RBAC Specification](docs/rbac.md)
