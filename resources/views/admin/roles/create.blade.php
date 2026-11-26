@extends('layouts.admin', ['title' => 'Create Role', 'header' => 'New Super Admin Role'])

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900">Create Super Admin Role</h1>
            <a href="{{ route('admin.roles.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">&larr; Back to Roles</a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Role Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Platform Auditor"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Status *</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Description</label>
                        <textarea name="description" rows="2" placeholder="Brief explanation of this role's access responsibilities"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">{{ old('description') }}</textarea>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Permission Matrix -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Permission Matrix</h3>
                            <p class="text-xs text-slate-500">Assign Super Admin permissions to this role</p>
                        </div>
                        <label class="flex items-center gap-2 text-xs font-bold text-teal-700 cursor-pointer bg-teal-50 px-3 py-1.5 rounded-lg border border-teal-200">
                            <input type="checkbox" id="select-all-permissions" class="w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                            <span>Select All Permissions</span>
                        </label>
                    </div>

                    <div class="space-y-6">
                        @foreach($permissionsByModule as $module => $permissions)
                            <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200/60">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-200/60 mb-3">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-950">{{ ucfirst($module) }} Module</h4>
                                    <label class="flex items-center gap-1.5 text-xs text-slate-600 font-semibold cursor-pointer">
                                        <input type="checkbox" class="module-select-all" data-module="{{ $module }}">
                                        <span>Select All</span>
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($permissions as $permission)
                                        <label class="flex items-start gap-2.5 p-2.5 bg-white rounded-lg border border-slate-100 shadow-2xs hover:border-teal-300 cursor-pointer transition-colors">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" data-module="{{ $module }}"
                                                class="permission-checkbox mt-0.5 w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500"
                                                {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <div>
                                                <div class="text-xs font-bold text-slate-800">{{ $permission->name }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $permission->slug }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('admin.roles.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-md">Create Role</button>
                </div>
            </form>
        </div>
    </div>
@endsection
