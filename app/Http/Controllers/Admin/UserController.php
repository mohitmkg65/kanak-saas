<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleScope;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserRoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserRoleService $userRoleService
    ) {}

    public function index(Request $request): View
    {
        $query = User::whereHas('userRoles.role', function ($q) {
            $q->where('scope', RoleScope::SUPER_ADMIN->value);
        });

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->with('userRoles.role')->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::where('scope', RoleScope::SUPER_ADMIN->value)
            ->where('status', 'active')
            ->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->userRoleService->createSuperAdminUser($data, (int) $data['role_id']);

        return redirect()->route('admin.users.index')->with('success', 'Super Admin user created successfully.');
    }

    public function edit(User $user): View
    {
        $roles = Role::where('scope', RoleScope::SUPER_ADMIN->value)
            ->where('status', 'active')
            ->get();

        $currentRoleId = $user->userRoles()
            ->whereNull('organization_id')
            ->first()?->role_id;

        return view('admin.users.edit', compact('user', 'roles', 'currentRoleId'));
    }

    public function update(UpdateAdminUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $this->userRoleService->updateUser($user, $data, (int) $data['role_id']);

        return redirect()->route('admin.users.index')->with('success', 'Super Admin user updated successfully.');
    }
}
