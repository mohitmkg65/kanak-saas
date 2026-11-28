@extends('layouts.organization', ['title' => 'Edit Product', 'header' => 'Inventory: Edit Product'])

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('organization.products.show', $product) }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1 mb-2">
                ← Back to Product Details
            </a>
            <h1 class="text-xl font-bold text-slate-900">Edit Product: {{ $product->name }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">Adjust pricing, stock alerts, or update material specifications</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs">
            <form action="{{ route('organization.products.update', $product) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                @if($product->isFromCatalog())
                    <div class="p-3 bg-indigo-50/70 border border-indigo-100 rounded-xl text-xs text-indigo-900 flex items-center justify-between">
                        <div>
                            <span class="font-bold">Catalog Linked:</span> Global benchmark price is ₹ {{ number_format($product->product?->base_price, 2) }}.
                        </div>
                        <span class="px-2 py-0.5 bg-indigo-100 font-bold rounded text-[10px]">Verified Spec</span>
                    </div>
                @endif

                <!-- Store Pricing -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Store Pricing & Margins</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Selling Price (₹) *</label>
                            <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Purchase / Cost Price (₹)</label>
                            <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">MRP (₹)</label>
                            <input type="number" step="0.01" name="mrp" value="{{ old('mrp', $product->mrp) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                </div>

                <!-- Product Specifications -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Material Specifications</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Product Title *</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Category *</label>
                            <select name="category_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Unit of Measure *</label>
                            <select name="unit_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}" {{ old('unit_id', $product->unit_id) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->abbreviation }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Brand Name</label>
                            <input type="text" name="brand" value="{{ old('brand', $product->brand) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Store SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">HSN Code</label>
                            <input type="text" name="hsn_code" value="{{ old('hsn_code', $product->hsn_code) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">GST Tax Rate (%)</label>
                            <select name="tax_rate" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <option value="0" {{ old('tax_rate', (int)$product->tax_rate) == 0 ? 'selected' : '' }}>0% (Exempt)</option>
                                <option value="5" {{ old('tax_rate', (int)$product->tax_rate) == 5 ? 'selected' : '' }}>5%</option>
                                <option value="12" {{ old('tax_rate', (int)$product->tax_rate) == 12 ? 'selected' : '' }}>12%</option>
                                <option value="18" {{ old('tax_rate', (int)$product->tax_rate) == 18 ? 'selected' : '' }}>18%</option>
                                <option value="28" {{ old('tax_rate', (int)$product->tax_rate) == 28 ? 'selected' : '' }}>28%</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Stock Levels -->
                <div class="pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Stock & Thresholds</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Current Recorded Stock</label>
                            <input type="number" step="0.01" name="opening_stock" value="{{ old('opening_stock', $product->opening_stock) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Minimum / Reorder Level</label>
                            <input type="number" step="0.01" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('organization.products.show', $product) }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-xs">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
