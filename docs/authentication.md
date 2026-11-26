# Authentication Architecture

## Overview

The application utilizes **one unified authentication system** operating on a single `users` database table. Both Super Admin system users and Organization team members authenticate through `/login`.

## Login Flow

1. User submits credentials via `POST /login`.
2. System validates email and password.
3. User status is verified (`active`). Inactive/suspended users are logged out immediately.
4. Access context determination:
   - If user has a `SUPER_ADMIN` role: Redirected to `/admin/dashboard`.
   - If user belongs to **exactly one active organization**: Active organization context is set in session (`OrganizationContextService`), and user is redirected to `/organization/dashboard`.
   - If user belongs to **multiple active organizations**: User is redirected to `/select-organization` to explicitly pick an organization context.
   - If user has no active memberships or super admin privileges: Logout and display message.
