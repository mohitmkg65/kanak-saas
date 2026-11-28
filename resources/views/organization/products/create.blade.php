@extends('layouts.organization', ['title' => 'Add Product', 'header' => 'Inventory: Add Product'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('organization.products.index') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
                ← Back to My Products
            </a>
            <h1 class="text-xl font-bold text-slate-900">
                {{ $catalogProduct ? 'Add from Catalog: ' . $catalogProduct->name : 'Create Custom Shop Product' }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $catalogProduct ? 'Set your selling price and initial stock for this standard catalog material' : 'Add custom local, unbranded, or specialized items unique to your business' }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <form action="{{ route('organization.products.store') }}" method="POST" class="space-y-6">
                @csrf

                @if($catalogProduct)
                    <input type="hidden" name="product_id" value="{{ $catalogProduct->id }}">
                    <div class="p-4 bg-indigo-50/60 border border-indigo-100 rounded-xl flex items-center justify-between">
                        <div class="text-xs">
                            <span class="text-indigo-600 font-bold uppercase tracking-wider block text-[10px]">Linked to Global Catalog</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $catalogProduct->name }}</span>
                            <div class="text-slate-500 mt-0.5">Benchmark Price: ₹ {{ number_format($catalogProduct->base_price, 2) }} • Unit: {{ $catalogProduct->unit?->name }}</div>
                        </div>
                        <a href="{{ route('organization.products.create') }}" class="text-xs text-indigo-700 hover:underline font-semibold">
                            Switch to Custom Product
                        </a>
                    </div>
                @endif

                <!-- Product Specifications -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Product Specifications</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Product Title *</label>
                            <input type="text" name="name" value="{{ old('name', $catalogProduct?->name) }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Category *</label>
                            <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">— Select Category —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $catalogProduct?->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Unit of Measure *</label>
                            <select name="unit_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="">— Select Unit —</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ old('unit_id', $catalogProduct?->unit_id) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->abbreviation }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Brand Name / Manufacturer</label>
                            <input type="text" name="brand" value="{{ old('brand', $catalogProduct?->brand) }}" placeholder="e.g. UltraTech, local, custom"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Store SKU / Item Code</label>
                            <input type="text" name="sku" value="{{ old('sku', $catalogProduct?->sku) }}" placeholder="e.g. ITEM-001"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">HSN Code</label>
                            <input type="text" name="hsn_code" value="{{ old('hsn_code', $catalogProduct?->hsn_code) }}" placeholder="e.g. 252329"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">GST Tax Rate (%)</label>
                            <select name="tax_rate" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="0" {{ old('tax_rate') == '0' ? 'selected' : '' }}>0% (Exempt)</option>
                                <option value="5" {{ old('tax_rate') == '5' ? 'selected' : '' }}>5%</option>
                                <option value="12" {{ old('tax_rate') == '12' ? 'selected' : '' }}>12%</option>
                                <option value="18" {{ old('tax_rate', '18') == '18' ? 'selected' : '' }}>18%</option>
                                <option value="28" {{ old('tax_rate') == '28' ? 'selected' : '' }}>28%</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Store Pricing -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Your Store Pricing</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Selling Price (₹) *</label>
                            <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $catalogProduct?->base_price) }}" required placeholder="0.00"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Purchase / Cost Price (₹)</label>
                            <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" placeholder="0.00"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">MRP (₹)</label>
                            <input type="number" step="0.01" name="mrp" value="{{ old('mrp') }}" placeholder="0.00"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                </div>

                <!-- Stock & Inventory Defaults -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Initial Stock Setup</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Opening Stock Quantity</label>
                            <input type="number" step="0.01" name="opening_stock" value="{{ old('opening_stock', 0) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Reorder / Minimum Stock Alert</label>
                            <input type="number" step="0.01" name="min_stock_level" value="{{ old('min_stock_level') }}" placeholder="e.g. 50"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('organization.products.index') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Save to Store Catalog
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
