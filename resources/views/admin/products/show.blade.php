@extends('layouts.admin', ['title' => $product->name, 'header' => 'Master Data: Product Details'])

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
            ← Back to Global Products
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $product->name }}</h1>
                <p class="text-xs text-slate-500 font-mono mt-0.5">SKU: {{ $product->sku ?? '—' }} &bull; Public ID: {{ $product->public_id }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.products.edit', $product) }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                    Edit Product
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Material Specifications</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Brand / Manufacturer</span>
                        <span class="font-semibold text-slate-800">{{ $product->brandRelation?->name ?? ($product->brand ?? 'Generic') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Category</span>
                        <span class="font-semibold text-slate-800">
                            {{ $product->category?->parent ? $product->category->parent->name . ' → ' : '' }}{{ $product->category?->name ?? '—' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Unit of Measure</span>
                        <span class="font-semibold text-slate-800">{{ $product->unit?->name }} ({{ $product->unit?->abbreviation }})</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">HSN Code</span>
                        <span class="font-semibold font-mono text-slate-800">{{ $product->hsn_code ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Benchmark Market Price</span>
                        <span class="font-bold text-slate-900 text-sm">
                            {{ $product->base_price ? '₹ ' . number_format($product->base_price, 2) : '—' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Status</span>
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $product->status === 'active' ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>
                </div>

                @if($product->description)
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <span class="text-slate-400 text-xs block mb-1">Description</span>
                        <p class="text-xs text-slate-700 leading-relaxed">{{ $product->description }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Adoption Stats</h3>
                <div class="p-4 bg-teal-50 border border-teal-100 rounded-xl">
                    <span class="text-xs text-teal-700 block mb-1">Active in Tenant Catalogs</span>
                    <span class="text-2xl font-black text-teal-900">{{ $product->organizationProducts()->count() }}</span>
                    <span class="text-[11px] text-teal-600 block mt-0.5">Organizations currently sell this item</span>
                </div>
            </div>
        </div>
    </div>
@endsection
