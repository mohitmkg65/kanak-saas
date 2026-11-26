<?php

namespace App\Http\Controllers\Organization;

use App\Enums\RoleScope;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrgUserRequest;
use App\Http\Requests\Organization\UpdateOrgUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\OrganizationContextService;
use App\Services\UserRoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected OrganizationContextService $context,
        protected UserRoleService $userRoleService
    ) {}

    public function index(Request $request): View
    {
        $org = $this->context->current();

        $query = User::whereHas('memberships', function ($q) use ($org) {
            $q->where('organization_id', $org->id);
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

        $users = $query->with(['userRoles' => function ($q) use ($org) {
            $q->where('organization_id', $org->id)->with('role');
        }])->latest()->paginate(10)->withQueryString();

        return view('organization.users.index', compact('users', 'org'));
    }

    public function create(): View
    {
        $org = $this->context->current();

        $roles = Role::where('scope', RoleScope::ORGANIZATION->value)
            ->where('organization_id', $org->id)
            ->where('status', 'active')
            ->get();

        return view('organization.users.create', compact('roles', 'org'));
    }

    public function store(StoreOrgUserRequest $request): RedirectResponse
    {
        $org = $this->context->current();
        $data = $request->validated();

        $this->userRoleService->createOrganizationUser($data, (int) $data['role_id'], $org);

        return redirect()->route('organization.users.index')->with('success', 'System user created successfully.');
    }

    public function edit(User $user): View
    {
        $org = $this->context->current();

        if (! $user->isMemberOf($org->id)) {
            abort(403, 'Unauthorized. User does not belong to your organization.');
        }

        $roles = Role::where('scope', RoleScope::ORGANIZATION->value)
            ->where('organization_id', $org->id)
            ->where('status', 'active')
            ->get();

        $currentRoleId = $user->userRoles()
            ->where('organization_id', $org->id)
            ->first()?->role_id;

        return view('organization.users.edit', compact('user', 'roles', 'currentRoleId', 'org'));
    }

    public function update(UpdateOrgUserRequest $request, User $user): RedirectResponse
    {
        $org = $this->context->current();

        if (! $user->isMemberOf($org->id)) {
            abort(403, 'Unauthorized. User does not belong to your organization.');
        }

        $data = $request->validated();
        $this->userRoleService->updateUser($user, $data, (int) $data['role_id'], $org);

        return redirect()->route('organization.users.index')->with('success', 'System user updated successfully.');
    }
}
