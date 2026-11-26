@extends('layouts.organization', ['title' => 'Roles & Permissions', 'header' => 'Roles & Permissions'])

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Organization Roles</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage custom business roles and permissions for {{ $org->name }}</p>
        </div>
        <a href="{{ route('organization.roles.create') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            + Create Role
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <form method="GET" action="{{ route('organization.roles.index') }}" class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by role name..."
                class="w-full sm:w-72 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">

            <select name="status" class="w-full sm:w-40 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl">
                Filter
            </button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Role Name</th>
                        <th class="px-6 py-3.5">Type</th>
                        <th class="px-6 py-3.5">Permissions</th>
                        <th class="px-6 py-3.5">Assigned Users</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($roles as $role)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $role->name }}</div>
                                <div class="text-xs text-slate-400">{{ $role->description ?? 'No description' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($role->is_system)
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 font-bold rounded border border-indigo-200">System Role</span>
                                @else
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-semibold rounded">Custom Role</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-teal-700">{{ $role->permissions_count }} permissions</td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-700">{{ $role->user_roles_count }} users</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $role->status === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ ucfirst($role->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <a href="{{ route('organization.roles.edit', $role) }}" class="text-xs font-bold text-teal-600 hover:text-teal-800">Edit Matrix</a>
                                @if(! $role->is_system && $role->user_roles_count === 0)
                                    <form action="{{ route('organization.roles.destroy', $role) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">No organization roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($roles->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
@endsection
