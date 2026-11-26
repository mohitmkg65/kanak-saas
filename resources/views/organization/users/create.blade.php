@extends('layouts.organization', ['title' => 'Create Team User', 'header' => 'New Team User'])

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900">Add Team User</h1>
            <a href="{{ route('organization.users.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">&larr; Back to Users</a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <!-- Read-only Organization Banner -->
            <div class="mb-6 p-3.5 bg-indigo-50 border border-indigo-200/60 rounded-xl flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-bold text-indigo-500 tracking-wider">Organization Context</span>
                    <div class="text-xs font-bold text-indigo-950">{{ $org->name }}</div>
                </div>
                <span class="px-2.5 py-1 bg-indigo-900 text-teal-300 text-[10px] font-bold rounded-lg uppercase">Read-Only</span>
            </div>

            <form method="POST" action="{{ route('organization.users.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Password *</label>
                        <input type="password" name="password" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Organization Role *</label>
                        <select name="role_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="">Select Organization Role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Status *</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('organization.users.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-md">Add User</button>
                </div>
            </form>
        </div>
    </div>
@endsection
