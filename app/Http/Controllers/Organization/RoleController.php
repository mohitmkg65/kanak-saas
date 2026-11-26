<?php

namespace App\Http\Controllers\Organization;

use App\Enums\RoleScope;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrgRoleRequest;
use App\Http\Requests\Organization\UpdateOrgRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\OrganizationContextService;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        protected OrganizationContextService $context,
        protected RoleService $roleService
    ) {}

    public function index(Request $request): View
    {
        $org = $this->context->current();

        $query = Role::where('scope', RoleScope::ORGANIZATION->value)
            ->where('organization_id', $org->id);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $roles = $query->withCount(['permissions', 'userRoles'])->latest()->paginate(10)->withQueryString();

        return view('organization.roles.index', compact('roles', 'org'));
    }

    public function create(): View
    {
        $org = $this->context->current();

        $permissionsByModule = Permission::where('scope', RoleScope::ORGANIZATION->value)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('module');

        return view('organization.roles.create', compact('permissionsByModule', 'org'));
    }

    public function store(StoreOrgRoleRequest $request): RedirectResponse
    {
        $org = $this->context->current();
        $data = $request->validated();

        $this->roleService->createRole($data, RoleScope::ORGANIZATION, $org->id, Auth::user());

        return redirect()->route('organization.roles.index')->with('success', 'Organization role created successfully.');
    }

    public function edit(Role $role): View
    {
        $org = $this->context->current();

        if ($role->scope !== RoleScope::ORGANIZATION || $role->organization_id !== $org->id) {
            abort(403, 'Unauthorized. Access to this role is forbidden.');
        }

        $permissionsByModule = Permission::where('scope', RoleScope::ORGANIZATION->value)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('module');

        $assignedPermissionIds = $role->permissions->pluck('id')->toArray();

        return view('organization.roles.edit', compact('role', 'permissionsByModule', 'assignedPermissionIds', 'org'));
    }

    public function update(UpdateOrgRoleRequest $request, Role $role): RedirectResponse
    {
        $org = $this->context->current();

        if ($role->scope !== RoleScope::ORGANIZATION || $role->organization_id !== $org->id) {
            abort(403, 'Unauthorized. Access to this role is forbidden.');
        }

        $data = $request->validated();
        $this->roleService->updateRole($role, $data, RoleScope::ORGANIZATION);

        return redirect()->route('organization.roles.index')->with('success', 'Organization role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $org = $this->context->current();

        if ($role->scope !== RoleScope::ORGANIZATION || $role->organization_id !== $org->id) {
            abort(403, 'Unauthorized. Access to this role is forbidden.');
        }

        $result = $this->roleService->deleteRole($role);

        if (! $result['success']) {
            return back()->withErrors(['role' => $result['message']]);
        }

        return redirect()->route('organization.roles.index')->with('success', $result['message']);
    }
}
