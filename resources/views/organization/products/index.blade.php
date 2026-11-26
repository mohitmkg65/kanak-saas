@extends('layouts.organization', ['title' => 'My Products', 'header' => 'Inventory: My Products'])

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-slate-900">My Product Catalog</h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage materials in your store, customize selling prices and stock levels</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('organization.products.catalog') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Browse Global Catalog
            </a>
            <a href="{{ route('organization.products.create') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-sm transition-all">
                + Add Custom Product
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('organization.products.index') }}" class="p-4 border-b border-slate-100 flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, SKU, brand..."
                class="w-full sm:w-64 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
            
            <select name="category_id" class="w-full sm:w-44 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <select name="source" class="w-full sm:w-40 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Sources</option>
                <option value="catalog" {{ request('source') === 'catalog' ? 'selected' : '' }}>Global Catalog</option>
                <option value="custom" {{ request('source') === 'custom' ? 'selected' : '' }}>Custom / Local</option>
            </select>

            <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-indigo-900 hover:bg-indigo-950 text-white text-xs font-bold rounded-xl">
                Filter
            </button>
            @if(request()->hasAny(['search', 'category_id', 'source']))
                <a href="{{ route('organization.products.index') }}" class="text-xs text-slate-500 hover:text-slate-800">Clear</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Product & Brand</th>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">Source</th>
                        <th class="px-6 py-3.5">Selling Price</th>
                        <th class="px-6 py-3.5">Purchase Price</th>
                        <th class="px-6 py-3.5">Unit</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('organization.products.show', $product) }}" class="font-bold text-slate-900 hover:text-teal-600">
                                    {{ $product->name }}
                                </a>
                                <div class="text-xs text-slate-400">
                                    {{ $product->brand ? $product->brand . ' • ' : '' }}SKU: <span class="font-mono">{{ $product->sku ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-700">
                                {{ $product->category?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($product->isFromCatalog())
                                    <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Global Catalog
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[11px] font-bold rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                                        Custom / Local
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-slate-900">
                                {{ $product->selling_price ? '₹ ' . number_format($product->selling_price, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $product->purchase_price ? '₹ ' . number_format($product->purchase_price, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                {{ $product->unit?->abbreviation ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $product->is_active ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $product->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('organization.products.show', $product) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">View</a>
                                <span class="text-slate-200">|</span>
                                <a href="{{ route('organization.products.edit', $product) }}" class="text-xs font-bold text-teal-600 hover:text-teal-800">Edit Price</a>
                                <span class="text-slate-200">|</span>
                                <form action="{{ route('organization.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Remove {{ $product->name }} from your store?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No products in your store yet.
                                <div class="mt-2 flex items-center justify-center gap-3">
                                    <a href="{{ route('organization.products.catalog') }}" class="text-xs font-bold text-indigo-600 hover:underline">
                                        Browse Global Catalog →
                                    </a>
                                    <span class="text-slate-300">or</span>
                                    <a href="{{ route('organization.products.create') }}" class="text-xs font-bold text-teal-600 hover:underline">
                                        Create Custom Item →
                                    </a>
                                </div>
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
