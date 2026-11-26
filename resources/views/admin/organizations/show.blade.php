@extends('layouts.admin', ['title' => $organization->name, 'header' => 'Organization Profile'])

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.organizations.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
            ← Back to Organizations
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $organization->name }}</h1>
                <p class="text-xs text-slate-500 font-mono mt-0.5">Slug: {{ $organization->slug }} &bull; Public ID: {{ $organization->public_id }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.organizations.edit', $organization) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    Edit Details
                </a>
            </div>
        </div>
    </div>

    <!-- Approval Banner / Actions -->
    <div class="mb-6 p-5 rounded-2xl border bg-white shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                @php
                    $approval = $organization->approval_status;
                    $badgeClass = match($approval?->value) {
                        'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
                        'under_review' => 'bg-blue-100 text-blue-800 border-blue-300',
                        'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
                        'suspended' => 'bg-purple-100 text-purple-800 border-purple-300',
                        default => 'bg-slate-100 text-slate-800 border-slate-300',
                    };
                @endphp
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Approval State</span>
                    <span class="px-3 py-1 text-xs font-extrabold rounded-full border {{ $badgeClass }}">
                        {{ $approval ? $approval->label() : 'Pending' }}
                    </span>
                </div>

                @if($organization->approved_at)
                    <div class="pl-4 border-l border-slate-200 text-xs text-slate-500">
                        <div>Approved by: <span class="font-semibold text-slate-800">{{ $organization->approvedBy?->name ?? 'System' }}</span></div>
                        <div>Date: <span class="font-semibold text-slate-800">{{ $organization->approved_at->format('M d, Y h:i A') }}</span></div>
                    </div>
                @endif
            </div>

            <!-- Approval Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @if($organization->approval_status?->value !== 'approved')
                    <form action="{{ route('admin.organizations.approve', $organization) }}" method="POST" onsubmit="return confirm('Approve this organization and grant full tenant access?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            ✓ Approve Organization
                        </button>
                    </form>
                @endif

                @if($organization->approval_status?->value === 'pending')
                    <form action="{{ route('admin.organizations.review', $organization) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition">
                            Mark Under Review
                        </button>
                    </form>
                @endif

                @if($organization->approval_status?->value !== 'suspended')
                    <button type="button" onclick="document.getElementById('suspend-modal').classList.remove('hidden')" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                        Suspend
                    </button>
                @endif

                @if($organization->approval_status?->value !== 'rejected')
                    <button type="button" onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition">
                        Reject
                    </button>
                @endif
            </div>
        </div>

        @if($organization->rejection_reason)
            <div class="mt-4 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800">
                <span class="font-bold">Rejection/Suspension Reason:</span> {{ $organization->rejection_reason }}
            </div>
        @endif
    </div>

    <!-- 2 Column Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Business Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Business Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Legal Name</span>
                        <span class="font-semibold text-slate-800">{{ $organization->legal_name ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Contact Email</span>
                        <span class="font-semibold text-slate-800">{{ $organization->email ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Phone Number</span>
                        <span class="font-semibold text-slate-800">{{ $organization->phone ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Owner Email</span>
                        <span class="font-semibold text-slate-800">{{ $organization->owner_email ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Location</span>
                        <span class="font-semibold text-slate-800">{{ $organization->city ?? '' }}{{ $organization->state ? ', ' . $organization->state : '' }}{{ $organization->country ? ', ' . $organization->country : '' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Postal Code</span>
                        <span class="font-semibold text-slate-800">{{ $organization->postal_code ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Currency / Locale</span>
                        <span class="font-semibold text-slate-800">{{ $organization->currency }} / {{ $organization->locale }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Timezone</span>
                        <span class="font-semibold text-slate-800">{{ $organization->timezone }}</span>
                    </div>
                </div>
            </div>

            <!-- Members -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Members & Staff ({{ $organization->memberships->count() }})</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 uppercase font-semibold text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">User</th>
                                <th class="px-5 py-3">Phone</th>
                                <th class="px-5 py-3">Membership Status</th>
                                <th class="px-5 py-3">Joined At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($organization->memberships as $m)
                                <tr>
                                    <td class="px-5 py-3 font-semibold text-slate-900">
                                        {{ $m->user?->name ?? '—' }}
                                        <div class="text-[11px] text-slate-400 font-normal">{{ $m->user?->email }}</div>
                                    </td>
                                    <td class="px-5 py-3">{{ $m->user?->phone ?? '—' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 font-bold rounded-full text-[10px] {{ $m->status === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ ucfirst($m->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-slate-400">{{ $m->joined_at ? $m->joined_at->format('M d, Y') : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-slate-400">No members registered yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Summary Cards -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Tenant Stats</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <span class="text-xs text-slate-600">Customers Enrolled</span>
                        <span class="text-sm font-bold text-slate-900">{{ $organization->customers->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <span class="text-xs text-slate-600">Dynamic Roles</span>
                        <span class="text-sm font-bold text-slate-900">{{ $organization->roles->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
            <h3 class="text-base font-bold text-slate-900 mb-2">Reject Organization</h3>
            <p class="text-xs text-slate-500 mb-4">Provide a reason for rejecting this onboarding application. The reason will be saved in the audit log.</p>
            <form action="{{ route('admin.organizations.reject', $organization) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Reason for Rejection *</label>
                    <textarea name="rejection_reason" rows="3" required placeholder="e.g. Incomplete business documents or duplicate registration..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="px-3.5 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl">
                        Confirm Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Suspend Modal -->
    <div id="suspend-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
            <h3 class="text-base font-bold text-slate-900 mb-2">Suspend Organization</h3>
            <p class="text-xs text-slate-500 mb-4">Suspending this organization will block all tenant members from logging into the business panel.</p>
            <form action="{{ route('admin.organizations.suspend', $organization) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Reason for Suspension *</label>
                    <textarea name="rejection_reason" rows="3" required placeholder="e.g. Non-payment, violation of terms, or requested by authority..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-800"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('suspend-modal').classList.add('hidden')" class="px-3.5 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl">
                        Confirm Suspension
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
