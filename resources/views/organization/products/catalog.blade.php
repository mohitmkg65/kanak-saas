@extends('layouts.organization', ['title' => 'Global Catalog', 'header' => 'Inventory: Browse Global Catalog'])

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('organization.products.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
                ← Back to My Products
            </a>
            <h1 class="text-xl font-bold text-slate-900">Global Product Catalog</h1>
            <p class="text-xs text-slate-500 mt-0.5">Select standard branded building materials to sell in {{ $org->name }}</p>
        </div>
        <a href="{{ route('organization.products.create') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            + Or Create Custom Item
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('organization.products.catalog') }}" class="p-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search catalog by material, brand, SKU..."
                class="w-full sm:w-72 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
            
            <select name="category_id" class="w-full sm:w-48 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl">
                Filter
            </button>
            @if(request()->hasAny(['search', 'category_id']))
                <a href="{{ route('organization.products.catalog') }}" class="text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Material & Brand</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">Unit</th>
                        <th class="px-6 py-3.5">HSN Code</th>
                        <th class="px-6 py-3.5">Benchmark Price</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($catalogProducts as $item)
                        @php
                            $isAdded = in_array($item->id, $addedProductIds);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $isAdded ? 'bg-slate-50/40' : '' }}">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $item->name }}</div>
                                <div class="text-xs text-slate-400">
                                    {{ $item->brand ? $item->brand . ' • ' : '' }}SKU: <span class="font-mono">{{ $item->sku ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-700">
                                {{ $item->category?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                {{ $item->unit?->name }} ({{ $item->unit?->abbreviation }})
                            </td>
                            <td class="px-6 py-4 text-xs font-mono text-slate-500">
                                {{ $item->hsn_code ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900">
                                {{ $item->base_price ? '₹ ' . number_format($item->base_price, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($isAdded)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-200">
                                        ✓ In Your Store
                                    </span>
                                @else
                                    <a href="{{ route('organization.products.create', ['from_catalog' => $item->id]) }}"
                                        class="inline-flex items-center px-3.5 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg shadow-2xs transition">
                                        + Add to Store
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No products found matching criteria in global catalog.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($catalogProducts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $catalogProducts->links() }}
            </div>
        @endif
    </div>
@endsection
