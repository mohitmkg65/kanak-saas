@extends('layouts.organization', ['title' => 'Customers', 'header' => 'Customers Management'])

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Customers Directory</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage buyers, contractors, builders, and wholesale accounts for {{ $org->name }}</p>
        </div>
        <a href="{{ route('organization.customers.create') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            + Add New Customer
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('organization.customers.index') }}" class="p-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, GSTIN, company..."
                class="w-full sm:w-64 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
            
            <select name="customer_type" class="w-full sm:w-40 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Customer Types</option>
                @foreach($customerTypes as $type)
                    <option value="{{ $type->value }}" {{ request('customer_type') === $type->value ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="w-full sm:w-36 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="blacklisted" {{ request('status') === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl">
                Filter
            </button>
            @if(request()->hasAny(['search', 'customer_type', 'status']))
                <a href="{{ route('organization.customers.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Customer / Company</th>
                        <th class="px-6 py-3.5">Contact</th>
                        <th class="px-6 py-3.5">Type</th>
                        <th class="px-6 py-3.5">Credit Limit</th>
                        <th class="px-6 py-3.5">Outstanding</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('organization.customers.show', $customer) }}" class="font-bold text-slate-900 hover:text-teal-600">
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
                                <div class="font-semibold text-slate-800">{{ $customer->phone ?? '—' }}</div>
                                <div class="text-slate-400 text-[11px]">{{ $customer->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $customer->customer_type ? $customer->customer_type->label() : 'Retail' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900">
                                {{ $customer->credit_limit ? '₹ ' . number_format($customer->credit_limit, 2) : 'No Limit' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-amber-600">
                                ₹ {{ number_format($customer->outstanding_balance, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $customer->status?->value === 'active' ? 'bg-teal-100 text-teal-800' : ($customer->status?->value === 'blacklisted' ? 'bg-rose-100 text-rose-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ucfirst($customer->status?->value ?? 'Active') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('organization.customers.show', $customer) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">View</a>
                                <span class="text-slate-200">|</span>
                                <a href="{{ route('organization.customers.edit', $customer) }}" class="text-xs font-bold text-teal-600 hover:text-teal-800">Edit</a>
                                <span class="text-slate-200">|</span>
                                <form action="{{ route('organization.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Delete customer {{ $customer->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No customers added yet. Click "+ Add New Customer" to register your buyers.
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
