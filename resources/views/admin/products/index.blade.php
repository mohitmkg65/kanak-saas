@extends('layouts.admin', ['title' => 'Global Product Catalog', 'header' => 'Master Data: Global Products'])

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Global Products Catalog</h1>
            <p class="text-xs text-slate-500 mt-0.5">Central repository of branded building materials available to all tenants</p>
        </div>
        <a href="{{ route('admin.products.create') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
            + Add Global Product
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('admin.products.index') }}" class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, SKU, brand, HSN..."
                class="w-full sm:w-72 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
            
            <select name="category_id" class="w-full sm:w-48 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="w-full sm:w-36 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl">
                Filter
            </button>
            @if(request()->hasAny(['search', 'category_id', 'status']))
                <a href="{{ route('admin.products.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Product & Brand</th>
                        <th class="px-6 py-3.5">SKU / HSN</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">Unit</th>
                        <th class="px-6 py-3.5">Benchmark Price</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.products.show', $product) }}" class="font-bold text-slate-900 hover:text-teal-600">
                                    {{ $product->name }}
                                </a>
                                <div class="text-xs text-slate-400">
                                    {{ $product->brand ? 'Brand: ' . $product->brand : 'Generic' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="font-mono font-bold text-slate-800">{{ $product->sku ?? '—' }}</div>
                                <div class="font-mono text-slate-400 text-[11px]">HSN: {{ $product->hsn_code ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                {{ $product->category?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                {{ $product->unit?->name ?? '—' }} ({{ $product->unit?->abbreviation ?? '' }})
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900">
                                {{ $product->base_price ? '₹ ' . number_format($product->base_price, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $product->status === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.products.show', $product) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">View</a>
                                <span class="text-slate-200">|</span>
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-xs font-bold text-teal-600 hover:text-teal-800">Edit</a>
                                <span class="text-slate-200">|</span>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Delete product {{ $product->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No products found. Click "+ Add Global Product" to add standard catalog items.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
