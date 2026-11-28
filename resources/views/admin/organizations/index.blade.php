@extends('layouts.admin', ['title' => 'Organizations', 'header' => 'Organizations Management'])

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Organizations</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage building material SaaS tenant accounts, onboarding & approvals</p>
        </div>
        <a href="{{ route('admin.organizations.create') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            + Add Organization
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('admin.organizations.index') }}" class="p-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
            <div class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email or slug..." class="w-full sm:w-64 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                
                <select name="status" class="w-full sm:w-32 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Status (All)</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
    
                <select name="approval_status" class="w-full sm:w-36 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Approval (All)</option>
                    @foreach($approvalStatuses as $status)
                        <option value="{{ $status->value }}" {{ request('approval_status') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
    
                <select name="registration_source" class="w-full sm:w-36 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">Source (All)</option>
                    @foreach($registrationSources as $source)
                        <option value="{{ $source->value }}" {{ request('registration_source') === $source->value ? 'selected' : '' }}>
                            {{ $source->label() }}
                        </option>
                    @endforeach
                </select>
    
                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'approval_status', 'registration_source', 'subscription_status']))
                    <a href="{{ route('admin.organizations.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Clear</a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Organization</th>
                        <th class="px-6 py-3.5">Source</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Approval</th>
                        <th class="px-6 py-3.5">Subscription</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($organizations as $org)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.organizations.show', $org) }}" class="font-bold text-slate-900 hover:text-teal-600">
                                    {{ $org->name }}
                                </a>
                                <div class="text-xs text-slate-400 font-mono">{{ $org->slug }} &bull; {{ $org->city ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $src = $org->registration_source;
                                    $srcBadge = $src?->badgeClass() ?? 'bg-indigo-100 text-indigo-800 border-indigo-200';
                                    $srcLabel = $src?->label() ?? 'Self Registered';
                                @endphp
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full border {{ $srcBadge }}">
                                    {{ $srcLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $op = $org->status?->value ?? (string) $org->status;
                                    $opBadge = match($op) {
                                        'active' => 'bg-emerald-100 text-emerald-800',
                                        'suspended' => 'bg-purple-100 text-purple-800',
                                        'inactive' => 'bg-slate-100 text-slate-700',
                                        default => 'bg-amber-100 text-amber-800',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $opBadge }}">
                                    {{ ucfirst($op) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $approval = $org->approval_status;
                                    $badgeClass = match($approval?->value) {
                                        'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'under_review' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        'suspended' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        default => 'bg-slate-100 text-slate-800 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full border {{ $badgeClass }}">
                                    {{ $approval ? $approval->label() : 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $sub = $org->subscription_status;
                                    $subBadge = $sub?->badgeClass() ?? 'bg-cyan-100 text-cyan-800 border-cyan-300';
                                    $subLabel = $sub?->label() ?? 'Trialing';
                                @endphp
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full border {{ $subBadge }}">
                                    {{ $subLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.organizations.show', $org) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Review / Details</a>
                                <span class="text-slate-300">|</span>
                                <a href="{{ route('admin.organizations.edit', $org) }}" class="text-xs font-bold text-teal-600 hover:text-teal-800">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No organizations found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($organizations->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $organizations->links() }}
            </div>
        @endif
    </div>
@endsection
