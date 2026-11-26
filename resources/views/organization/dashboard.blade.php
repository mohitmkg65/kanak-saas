@extends('layouts.organization', ['title' => 'Dashboard', 'header' => 'Dashboard Overview'])

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Card 1: Products -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Store Products</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['products_count'] }}</h3>
                    <a href="{{ route('organization.products.index') }}" class="text-[11px] text-teal-600 font-semibold mt-1 hover:underline block">
                        Manage Catalog &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-lg">
                    📦
                </div>
            </div>
        </div>

        <!-- Card 2: Customers -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Customers</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['customers_count'] }}</h3>
                    <a href="{{ route('organization.customers.index') }}" class="text-[11px] text-indigo-600 font-semibold mt-1 hover:underline block">
                        View Directory &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-lg">
                    👥
                </div>
            </div>
        </div>

        <!-- Card 3: Team Staff -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Team Members</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $stats['system_users_count'] }}</h3>
                    <a href="{{ route('organization.users.index') }}" class="text-[11px] text-slate-500 font-semibold mt-1 hover:underline block">
                        Manage Staff &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-lg">
                    🛡️
                </div>
            </div>
        </div>

        <!-- Card 4: Plan -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Active Tier</p>
                    <h3 class="text-base font-bold text-teal-600 mt-1">{{ $stats['current_plan'] }}</h3>
                    <span class="text-[11px] text-slate-400 mt-1 block">Full Modules Access</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center font-bold text-lg">
                    ⚡
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Banner -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-gradient-to-br from-indigo-950 to-slate-900 text-white p-6 sm:p-8 rounded-2xl border border-indigo-900/60 shadow-lg">
            <div class="inline-flex items-center px-3 py-1 bg-teal-500/20 text-teal-300 rounded-full text-xs font-bold border border-teal-500/30 mb-4">
                Shop Operations Active
            </div>
            <h2 class="text-2xl font-bold mb-2">Welcome to {{ $org->name }}</h2>
            <p class="text-xs text-indigo-200 leading-relaxed mb-6">
                Start adding products from our global catalog of standard cements, steel, tiles, and paints, or add custom products unique to your shop. Enroll contractors, builders, and retail buyers with credit limits.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('organization.products.catalog') }}" class="px-4 py-2.5 bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                    Browse Global Catalog &rarr;
                </a>
                <a href="{{ route('organization.customers.create') }}" class="px-4 py-2.5 bg-indigo-800 hover:bg-indigo-700 text-indigo-100 font-bold text-xs rounded-xl border border-indigo-600 transition-all">
                    + Add New Customer
                </a>
                <a href="{{ route('organization.products.create') }}" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl border border-slate-700 transition-all">
                    + Add Custom Product
                </a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 mb-4">Organization Profile</h3>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Legal Name</span>
                    <span class="font-semibold text-slate-900">{{ $org->legal_name ?? $org->name }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Location</span>
                    <span class="font-semibold text-slate-900">{{ $org->city ?? '' }}{{ $org->state ? ', ' . $org->state : '' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Currency</span>
                    <span class="font-semibold text-slate-900">{{ $org->currency }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Timezone</span>
                    <span class="font-semibold text-slate-900">{{ $org->timezone }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100">
                    <span class="text-slate-500">Status</span>
                    <span class="px-2 py-0.5 bg-teal-100 text-teal-800 font-bold rounded-full uppercase text-[10px]">{{ $org->status->value }}</span>
                </div>
            </div>
            <a href="{{ route('organization.settings.index') }}" class="mt-4 block text-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl border border-slate-200">
                Edit Settings
            </a>
        </div>
    </div>
@endsection
