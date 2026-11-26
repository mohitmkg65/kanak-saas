<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleScope;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRoleRequest;
use App\Http\Requests\Admin\UpdateAdminRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService
    ) {}

    public function index(Request $request): View
    {
        $query = Role::where('scope', RoleScope::SUPER_ADMIN->value);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $roles = $query->withCount(['permissions', 'userRoles'])->latest()->paginate(10)->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissionsByModule = Permission::where('scope', RoleScope::SUPER_ADMIN->value)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('module');

        return view('admin.roles.create', compact('permissionsByModule'));
    }

    public function store(StoreAdminRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->roleService->createRole($data, RoleScope::SUPER_ADMIN, null, Auth::user());

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        if ($role->scope !== RoleScope::SUPER_ADMIN) {
            abort(403);
        }

        $permissionsByModule = Permission::where('scope', RoleScope::SUPER_ADMIN->value)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('module');

        $assignedPermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissionsByModule', 'assignedPermissionIds'));
    }

    public function update(UpdateAdminRoleRequest $request, Role $role): RedirectResponse
    {
        if ($role->scope !== RoleScope::SUPER_ADMIN) {
            abort(403);
        }

        $data = $request->validated();
        $this->roleService->updateRole($role, $data, RoleScope::SUPER_ADMIN);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->scope !== RoleScope::SUPER_ADMIN) {
            abort(403);
        }

        $result = $this->roleService->deleteRole($role);

        if (! $result['success']) {
            return back()->withErrors(['role' => $result['message']]);
        }

        return redirect()->route('admin.roles.index')->with('success', $result['message']);
    }
}
