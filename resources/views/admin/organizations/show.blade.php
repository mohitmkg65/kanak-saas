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

    <!-- Status Banner / Lifecycle Actions -->
    <div class="mb-6 p-5 rounded-2xl border bg-white shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <!-- 3 Distinct Status Badges + Source -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- Approval Status -->
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Approval State</span>
                    @php
                        $approval = $organization->approval_status;
                        $approvalBadge = match($approval?->value) {
                            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
                            'under_review' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
                            'suspended' => 'bg-purple-100 text-purple-800 border-purple-300',
                            default => 'bg-slate-100 text-slate-800 border-slate-300',
                        };
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-extrabold rounded-full border inline-block {{ $approvalBadge }}">
                        {{ $approval ? $approval->label() : 'Pending' }}
                    </span>
                    @if($organization->approved_at)
                        <div class="text-[10px] text-slate-400 mt-1">
                            Approved: {{ $organization->approved_at->format('M d, Y') }}
                        </div>
                    @endif
                </div>

                <!-- Operational Status -->
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Operational Status</span>
                    @php
                        $opStatus = $organization->status?->value ?? (string) $organization->status;
                        $opBadge = match($opStatus) {
                            'active' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'suspended' => 'bg-purple-100 text-purple-800 border-purple-300',
                            'inactive' => 'bg-slate-100 text-slate-700 border-slate-300',
                            default => 'bg-amber-100 text-amber-800 border-amber-300',
                        };
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-extrabold rounded-full border inline-block {{ $opBadge }}">
                        {{ ucfirst($opStatus) }}
                    </span>
                </div>

                <!-- Subscription Status -->
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Subscription</span>
                    @php
                        $sub = $organization->subscription_status;
                        $subBadge = $sub?->badgeClass() ?? 'bg-cyan-100 text-cyan-800 border-cyan-300';
                        $subLabel = $sub?->label() ?? 'Trialing';
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-extrabold rounded-full border inline-block {{ $subBadge }}">
                        {{ $subLabel }}
                    </span>
                </div>

                <!-- Registration Source -->
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Origin / Source</span>
                    @php
                        $source = $organization->registration_source;
                        $sourceBadge = $source?->badgeClass() ?? 'bg-indigo-100 text-indigo-800 border-indigo-200';
                        $sourceLabel = $source?->label() ?? 'Self Registered';
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-extrabold rounded-full border inline-block {{ $sourceBadge }}">
                        {{ $sourceLabel }}
                    </span>
                    @if($organization->createdBy)
                        <div class="text-[10px] text-slate-400 mt-1 truncate" title="{{ $organization->createdBy->name }}">
                            By: {{ $organization->createdBy->name }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval & Status Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 border-t lg:border-t-0 pt-3 lg:pt-0 border-slate-100">
                @if($organization->approval_status?->value === 'rejected')
                    {{-- Re-Approval Button for Rejected Organization --}}
                    <button type="button" onclick="document.getElementById('reapprove-modal').classList.remove('hidden')" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Re-Approve Organization
                    </button>
                @elseif($organization->approval_status?->value !== 'approved')
                    {{-- Fresh Approval Button --}}
                    <form action="{{ route('admin.organizations.approve', $organization) }}" method="POST" onsubmit="return confirm('Approve this organization and grant full tenant access?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1">
                            <span>✓</span> Approve Organization
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

                @if(($organization->status?->value ?? $organization->status) === 'suspended')
                    {{-- Reactivate / Unsuspend Button --}}
                    <button type="button" onclick="document.getElementById('reactivate-modal').classList.remove('hidden')" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Reactivate Account
                    </button>
                @elseif($organization->approval_status?->value === 'approved')
                    {{-- Suspend Button --}}
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

        @if($organization->rejection_reason && $organization->approval_status?->value === 'rejected')
            <div class="mt-4 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 flex items-start gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <span class="font-bold">Current Rejection Reason:</span> {{ $organization->rejection_reason }}
                    <span class="block text-[11px] text-rose-600 mt-0.5">You can re-approve this organization at any time once compliance requirements or documents have been rectified.</span>
                </div>
            </div>
        @endif
    </div>

    <!-- 2 Column Details & Audit History -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Column: Business Info & Logs -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Business Details -->
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
                        <span class="text-slate-400 block mb-0.5">Owner / Admin Email</span>
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

            <!-- Dedicated Organization Status & Lifecycle Logs -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Status & Lifecycle Audit Trail</h2>
                        <p class="text-[11px] text-slate-400">Complete historical record of all approvals, suspensions, rejections, and reactivations.</p>
                    </div>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg">
                        {{ $organization->statusLogs->count() }} {{ Str::plural('Event', $organization->statusLogs->count()) }}
                    </span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($organization->statusLogs as $log)
                        <div class="p-4 hover:bg-slate-50/50 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border {{ $log->actionBadgeClass() }}">
                                        {{ $log->actionLabel() }}
                                    </span>
                                    @if($log->actionBy)
                                        <span class="text-xs text-slate-600">
                                            by <strong class="text-slate-900">{{ $log->actionBy->name }}</strong>
                                            <span class="text-slate-400 text-[11px]">({{ $log->actionBy->email }})</span>
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">by System / Self-service</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-2">
                                    <span>{{ $log->created_at->format('M d, Y h:i A') }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            @if($log->from_status || $log->from_approval_status)
                                <div class="text-[11px] text-slate-500 mb-2 flex items-center gap-3">
                                    @if($log->from_approval_status !== $log->to_approval_status)
                                        <span>Approval: <code class="text-slate-700 bg-slate-100 px-1 rounded">{{ $log->from_approval_status ?? 'none' }}</code> → <code class="text-teal-700 bg-teal-50 px-1 rounded font-semibold">{{ $log->to_approval_status }}</code></span>
                                    @endif
                                    @if($log->from_status !== $log->to_status)
                                        <span>Status: <code class="text-slate-700 bg-slate-100 px-1 rounded">{{ $log->from_status ?? 'none' }}</code> → <code class="text-teal-700 bg-teal-50 px-1 rounded font-semibold">{{ $log->to_status }}</code></span>
                                    @endif
                                    @if($log->ip_address)
                                        <span class="text-slate-400">IP: {{ $log->ip_address }}</span>
                                    @endif
                                </div>
                            @endif

                            @if($log->reason)
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-700">
                                    <span class="font-semibold text-slate-900">Reason / Remarks:</span> {{ $log->reason }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400">
                            No lifecycle log entries recorded for this organization yet.
                        </div>
                    @endforelse
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
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                        <span class="text-xs text-slate-600">Audit Trail Entries</span>
                        <span class="text-sm font-bold text-slate-900">{{ $organization->statusLogs->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Re-Approve Modal -->
    <div id="reapprove-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
            <h3 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                <span class="text-teal-600">✓</span> Re-Approve Organization
            </h3>
            <p class="text-xs text-slate-500 mb-4">
                This organization was previously rejected. Re-approving will reinstate active operational status and restore full tenant system access.
            </p>
            <form action="{{ route('admin.organizations.reapprove', $organization) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Approval Remarks / Notes (Optional)</label>
                    <textarea name="remarks" rows="3" placeholder="e.g. Corrected business registration documents verified; application re-approved..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('reapprove-modal').classList.add('hidden')" class="px-3.5 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Confirm Re-Approval
                    </button>
                </div>
            </form>
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
                    <textarea name="suspension_reason" rows="3" required placeholder="e.g. Non-payment, violation of terms, or requested by authority..."
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

    <!-- Reactivate Modal -->
    <div id="reactivate-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
            <h3 class="text-base font-bold text-slate-900 mb-1 flex items-center gap-2">
                <span class="text-amber-600">⚡</span> Reactivate Organization
            </h3>
            <p class="text-xs text-slate-500 mb-4">
                This will un-suspend the organization and restore tenant access immediately.
            </p>
            <form action="{{ route('admin.organizations.reactivate', $organization) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Reactivation Remarks (Optional)</label>
                    <textarea name="remarks" rows="3" placeholder="e.g. Outstanding invoice settled, compliance issue resolved..."
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('reactivate-modal').classList.add('hidden')" class="px-3.5 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Confirm Reactivation
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
