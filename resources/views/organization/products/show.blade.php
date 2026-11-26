@extends('layouts.organization', ['title' => $product->name, 'header' => 'Inventory: Product Details'])

@section('content')
    <div class="mb-6">
        <a href="{{ route('organization.products.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
            ← Back to My Products
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ $product->name }}</h1>
                <p class="text-xs text-slate-500 font-mono mt-0.5">
                    SKU: {{ $product->sku ?? '—' }} &bull; Public ID: {{ $product->public_id }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('organization.products.edit', $product) }}" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                    Edit Pricing & Specs
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Item Details</h2>
                    @if($product->isFromCatalog())
                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                            ✓ Linked to Global Catalog Spec
                        </span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-bold rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                            Custom Shop Product
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">Brand / Manufacturer</span>
                        <span class="font-semibold text-slate-800">{{ $product->brand ?? 'Generic / Unbranded' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Category</span>
                        <span class="font-semibold text-slate-800">
                            {{ $product->category?->name ?? '—' }}
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
                        <span class="text-slate-400 block mb-0.5">Applicable GST Tax Rate</span>
                        <span class="font-semibold text-slate-800">{{ $product->tax_rate !== null ? $product->tax_rate . '%' : 'Not set' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">Catalog Status</span>
                        <span class="px-2.5 py-0.5 text-xs font-bold rounded-full {{ $product->is_active ? 'bg-teal-100 text-teal-800' : 'bg-slate-100 text-slate-700' }}">
                            {{ $product->is_active ? 'Active' : 'Disabled' }}
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

            <!-- Global Catalog Reference Card -->
            @if($product->product)
                <div class="bg-indigo-50/50 rounded-2xl border border-indigo-100 p-6 shadow-xs">
                    <h3 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2">Global Master Reference</h3>
                    <p class="text-xs text-indigo-700 mb-3">This product is matched to the central SaaS master catalog.</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                        <div class="bg-white p-3 rounded-xl border border-indigo-100">
                            <span class="text-[10px] text-slate-400 block uppercase">Standard Name</span>
                            <span class="font-semibold text-slate-900">{{ $product->product->name }}</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-indigo-100">
                            <span class="text-[10px] text-slate-400 block uppercase">Benchmark Price</span>
                            <span class="font-semibold text-slate-900">₹ {{ number_format($product->product->base_price, 2) }}</span>
                        </div>
                        <div class="bg-white p-3 rounded-xl border border-indigo-100">
                            <span class="text-[10px] text-slate-400 block uppercase">Master SKU</span>
                            <span class="font-mono font-semibold text-slate-900">{{ $product->product->sku }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Financial & Stock Breakdown -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Pricing Breakdown</h3>
                <div class="space-y-3">
                    <div class="p-4 bg-teal-50 border border-teal-100 rounded-xl">
                        <span class="text-xs text-teal-700 block mb-1">Selling Price (To Customers)</span>
                        <span class="text-xl font-black text-teal-900">
                            {{ $product->selling_price ? '₹ ' . number_format($product->selling_price, 2) : 'Not configured' }}
                        </span>
                        <span class="text-[11px] text-teal-600 block mt-0.5">per {{ $product->unit?->abbreviation }}</span>
                    </div>

                    <div class="p-3.5 bg-slate-50 rounded-xl flex items-center justify-between text-xs">
                        <span class="text-slate-500">Purchase / Cost Price</span>
                        <span class="font-bold text-slate-800">
                            {{ $product->purchase_price ? '₹ ' . number_format($product->purchase_price, 2) : '—' }}
                        </span>
                    </div>

                    <div class="p-3.5 bg-slate-50 rounded-xl flex items-center justify-between text-xs">
                        <span class="text-slate-500">Maximum Retail Price (MRP)</span>
                        <span class="font-bold text-slate-800">
                            {{ $product->mrp ? '₹ ' . number_format($product->mrp, 2) : '—' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Inventory Balance</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl text-xs">
                        <span class="text-slate-600">Opening Stock</span>
                        <span class="font-bold text-slate-900">{{ number_format($product->opening_stock, 2) }} {{ $product->unit?->abbreviation }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl text-xs">
                        <span class="text-slate-600">Reorder Alert Level</span>
                        <span class="font-bold text-slate-900">
                            {{ $product->min_stock_level ? number_format($product->min_stock_level, 2) . ' ' . $product->unit?->abbreviation : 'Not set' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
