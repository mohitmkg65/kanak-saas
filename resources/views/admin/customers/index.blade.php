@extends('layouts.admin', ['title' => 'Customer Directory', 'header' => 'Platform: Customer Directory'])

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Platform Customer Directory</h1>
            <p class="text-xs text-slate-500 mt-0.5">Read-only aggregate directory of contractors, dealers, and retail buyers across all tenant shops</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('admin.customers.index') }}" class="p-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, GSTIN, company..."
                class="w-full sm:w-64 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
            
            <select name="organization_id" class="w-full sm:w-48 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Organizations</option>
                @foreach($organizations as $org)
                    <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>
                        {{ $org->name }}
                    </option>
                @endforeach
            </select>

            <select name="customer_type" class="w-full sm:w-36 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Types</option>
                <option value="retail" {{ request('customer_type') === 'retail' ? 'selected' : '' }}>Retail</option>
                <option value="wholesale" {{ request('customer_type') === 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                <option value="contractor" {{ request('customer_type') === 'contractor' ? 'selected' : '' }}>Contractor</option>
                <option value="dealer" {{ request('customer_type') === 'dealer' ? 'selected' : '' }}>Dealer</option>
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl">
                Filter
            </button>
            @if(request()->hasAny(['search', 'organization_id', 'customer_type']))
                <a href="{{ route('admin.customers.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Customer / Company</th>
                        <th class="px-6 py-3.5">Tenant Shop</th>
                        <th class="px-6 py-3.5">Contact</th>
                        <th class="px-6 py-3.5">Type</th>
                        <th class="px-6 py-3.5">Credit Limit</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="font-bold text-slate-900 hover:text-teal-600">
                                    {{ $customer->name }}
                                </a>
                                @if($customer->company_name)
                                    <div class="text-xs text-slate-500">{{ $customer->company_name }}</div>
                                @endif
                                @if($customer->gstin)
                                    <div class="text-[10px] font-mono text-slate-400">GSTIN: {{ $customer->gstin }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <span class="font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">
                                    {{ $customer->organization?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div>{{ $customer->phone ?? '—' }}</div>
                                <div class="text-slate-400 text-[11px]">{{ $customer->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-slate-100 text-slate-700">
                                    {{ ucfirst($customer->customer_type?->value ?? 'Retail') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900">
                                {{ $customer->credit_limit ? '₹ ' . number_format($customer->credit_limit, 2) : 'No Limit' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $customer->status?->value === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($customer->status?->value ?? 'Active') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No customer records found across tenant shops.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
@endsection
