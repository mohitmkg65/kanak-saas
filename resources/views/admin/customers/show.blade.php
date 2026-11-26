@extends('layouts.admin', ['title' => $customer->name, 'header' => 'Platform: Customer Details'])

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.customers.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
            ← Back to Customer Directory
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $customer->name }}</h1>
                <p class="text-xs text-slate-500 font-mono mt-0.5">Tenant: {{ $customer->organization?->name }} &bull; Public ID: {{ $customer->public_id }}</p>
            </div>
            <div>
                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $customer->status?->value === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                    {{ ucfirst($customer->status?->value ?? 'Active') }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Customer Profile</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Full Name</span>
                        <span class="font-semibold text-slate-800">{{ $customer->name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Company Name</span>
                        <span class="font-semibold text-slate-800">{{ $customer->company_name ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Customer Type</span>
                        <span class="font-semibold text-slate-800">{{ ucfirst($customer->customer_type?->value ?? 'Retail') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">GSTIN</span>
                        <span class="font-semibold font-mono text-slate-800">{{ $customer->gstin ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Phone Number</span>
                        <span class="font-semibold text-slate-800">{{ $customer->phone ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Email</span>
                        <span class="font-semibold text-slate-800">{{ $customer->email ?? '—' }}</span>
                    </div>
                </div>

                @if($customer->notes)
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <span class="text-slate-400 text-xs block mb-1">Notes</span>
                        <p class="text-xs text-slate-700">{{ $customer->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Address Information -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Billing Address</h3>
                    <div class="text-xs text-slate-600 space-y-1">
                        <div>{{ $customer->billing_address ?? 'No address provided' }}</div>
                        <div>{{ $customer->billing_city }}{{ $customer->billing_state ? ', ' . $customer->billing_state : '' }}</div>
                        <div>{{ $customer->billing_postal_code }} {{ $customer->billing_country }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Shipping / Site Address</h3>
                    <div class="text-xs text-slate-600 space-y-1">
                        <div>{{ $customer->shipping_address ?? 'Same as billing' }}</div>
                        <div>{{ $customer->shipping_city }}{{ $customer->shipping_state ? ', ' . $customer->shipping_state : '' }}</div>
                        <div>{{ $customer->shipping_postal_code }} {{ $customer->shipping_country }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Financial Ledger Summary</h3>
                <div class="space-y-3">
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <span class="text-xs text-slate-500 block mb-1">Credit Limit</span>
                        <span class="text-lg font-bold text-slate-900">
                            {{ $customer->credit_limit ? '₹ ' . number_format($customer->credit_limit, 2) : 'No Limit Set' }}
                        </span>
                    </div>
                    <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl">
                        <span class="text-xs text-amber-700 block mb-1">Outstanding Balance</span>
                        <span class="text-lg font-bold text-amber-900">
                            ₹ {{ number_format($customer->outstanding_balance, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
