@extends('layouts.admin', ['title' => 'Edit Organization', 'header' => 'Edit Organization'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-bold text-slate-900">Edit Organization: {{ $organization->name }}</h1>
            <a href="{{ route('admin.organizations.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">&larr; Back to List</a>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.organizations.update', $organization) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Organization Name *</label>
                        <input type="text" name="name" value="{{ old('name', $organization->name) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Legal Name</label>
                        <input type="text" name="legal_name" value="{{ old('legal_name', $organization->legal_name) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $organization->slug) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Status *</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                            <option value="active" {{ old('status', $organization->status->value) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $organization->status->value) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status', $organization->status->value) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="pending" {{ old('status', $organization->status->value) === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Business Email</label>
                        <input type="email" name="email" value="{{ old('email', $organization->email) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Business Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $organization->phone) }}"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Currency</label>
                        <input type="text" name="currency" value="{{ old('currency', $organization->currency) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Timezone</label>
                        <input type="text" name="timezone" value="{{ old('timezone', $organization->timezone) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-700 mb-1.5">Locale</label>
                        <input type="text" name="locale" value="{{ old('locale', $organization->locale) }}" required
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('admin.organizations.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-md">Update Organization</button>
                </div>
            </form>
        </div>
    </div>
@endsection
