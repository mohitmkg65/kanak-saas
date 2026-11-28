@extends('layouts.admin', ['title' => 'Dashboard', 'header' => 'Dashboard Overview'])

@section('content')
    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Organizations</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_organizations'] }}</h3>
                    <div class="text-[11px] text-teal-600 font-medium mt-1">{{ $stats['active_organizations'] }} active approved</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-lg">
                    🏢
                </div>
            </div>
        </div>

        <!-- Card 2: Pending Approvals -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pending Approvals</p>
                    <h3 class="text-2xl font-bold {{ $stats['pending_organizations'] > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1">
                        {{ $stats['pending_organizations'] }}
                    </h3>
                    <div class="text-[11px] text-slate-400 mt-1">Applications awaiting review</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-lg">
                    ⏳
                </div>
            </div>
        </div>

        <!-- Card 3: Global Catalog Products -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Global Products</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_products'] }}</h3>
                    <div class="text-[11px] text-indigo-600 font-medium mt-1">{{ $stats['total_categories'] }} categories • {{ $stats['total_brands'] }} brands</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-lg">
                    📦
                </div>
            </div>
        </div>

        <!-- Card 4: Total Customers Across Platform -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Platform Buyers</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['total_customers'] }}</h3>
                    <div class="text-[11px] text-slate-400 mt-1">Enrolled across tenant shops</div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-lg">
                    👥
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Applications Alert Banner (if any) -->
    @if($stats['pending_organizations'] > 0)
        <div class="mb-8 p-5 bg-amber-50 border border-amber-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm">!</span>
                <div>
                    <h4 class="text-sm font-bold text-amber-900">{{ $stats['pending_organizations'] }} Tenant Application(s) Awaiting Review</h4>
                    <p class="text-xs text-amber-700 mt-0.5">Review tenant business documents and approve access to the SaaS platform.</p>
                </div>
            </div>
            <a href="{{ route('admin.organizations.index', ['approval_status' => 'pending']) }}"
                class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl whitespace-nowrap transition">
                Review Pending Applications &rarr;
            </a>
        </div>
    @endif

    <!-- Recent Organizations Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Recent Organizations</h3>
                <p class="text-xs text-slate-500 mt-0.5">Overview of newly registered SaaS organizations</p>
            </div>
            <a href="{{ route('admin.organizations.index') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700">View All &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Organization</th>
                        <th class="px-6 py-3.5">Slug</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Approval</th>
                        <th class="px-6 py-3.5">Created</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrganizations as $org)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900">
                                <a href="{{ route('admin.organizations.show', $org) }}" class="hover:text-teal-600">
                                    {{ $org->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-slate-500">{{ $org->slug }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $org->status->value === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ ucfirst($org->status->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $org->approval_status?->value === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $org->approval_status ? $org->approval_status->label() : 'Pending' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">{{ $org->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.organizations.show', $org) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-sm">No organizations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
